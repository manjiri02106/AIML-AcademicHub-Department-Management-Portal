<?php
/**
 * notices.php – Notices CRUD Page
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'config/database.php';

$page_title   = 'Notices';
$page_subtitle = 'Department notices and announcements';
$current_page  = 'notices';

$flash_msg = ''; $flash_type = '';

// ── DELETE ────────────────────────────────────────────────────
if (($_GET['action'] ?? '') === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT attachment FROM notices WHERE id=?");
    $stmt->bind_param('i', $id); $stmt->execute();
    $att = $stmt->get_result()->fetch_assoc()['attachment'] ?? null; $stmt->close();
    if ($att && file_exists(UPLOAD_DIR . 'notices/' . $att)) unlink(UPLOAD_DIR . 'notices/' . $att);
    $stmt = $conn->prepare("DELETE FROM notices WHERE id=?");
    $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();
    redirect('notices.php?msg=deleted');
}

// ── ADD / EDIT ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $title  = trim($_POST['title'] ?? '');
    $desc   = trim($_POST['description'] ?? '');
    $pdate  = $_POST['publish_date'] ?? date('Y-m-d');

    $att_name = $_POST['existing_attachment'] ?? null;
    if (!empty($_FILES['attachment']['name'])) {
        $dir = UPLOAD_DIR . 'notices/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf','doc','docx','jpg','jpeg','png']) && $_FILES['attachment']['size'] <= 5242880) {
            $fname = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['attachment']['name']));
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $dir . $fname)) {
                if ($att_name && file_exists($dir . $att_name)) unlink($dir . $att_name);
                $att_name = $fname;
            }
        }
    }

    if ($action === 'add') {
        $nid  = generateId($conn, 'notices', 'notice_id', 'NOT');
        $stmt = $conn->prepare("INSERT INTO notices (notice_id,title,description,publish_date,attachment) VALUES (?,?,?,?,?)");
        $stmt->bind_param('sssss', $nid,$title,$desc,$pdate,$att_name);
        $stmt->execute(); $stmt->close();
        redirect('notices.php?msg=added');
    } elseif ($action === 'edit') {
        $id   = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("UPDATE notices SET title=?,description=?,publish_date=?,attachment=? WHERE id=?");
        $stmt->bind_param('ssssi', $title,$desc,$pdate,$att_name,$id);
        $stmt->execute(); $stmt->close();
        redirect('notices.php?msg=updated');
    }
}

$msg_map = ['added'=>['Notice published successfully.','success'],
            'updated'=>['Notice updated.','success'],
            'deleted'=>['Notice deleted.','danger']];
if (isset($_GET['msg']) && isset($msg_map[$_GET['msg']])) {
    [$flash_msg, $flash_type] = $msg_map[$_GET['msg']];
}

$per_page = 10;
$page     = max(1, (int)($_GET['page'] ?? 1));
$search   = trim($_GET['search'] ?? '');
$sp       = "%$search%";

$where  = "WHERE (title LIKE ? OR description LIKE ?)";
$types  = 'ss'; $params = [$sp,$sp];

$cnt = $conn->prepare("SELECT COUNT(*) AS c FROM notices $where");
$cnt->bind_param($types, ...$params); $cnt->execute();
$total = $cnt->get_result()->fetch_assoc()['c']; $cnt->close();
$total_pages = max(1, ceil($total/$per_page));
$offset = ($page-1)*$per_page;

$d = $conn->prepare("SELECT * FROM notices $where ORDER BY publish_date DESC, created_at DESC LIMIT ? OFFSET ?");
$d->bind_param($types.'ii', ...array_merge($params,[$per_page,$offset]));
$d->execute();
$records = $d->get_result()->fetch_all(MYSQLI_ASSOC); $d->close();
$conn->close();

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div id="main-content">
<?php include 'includes/navbar.php'; ?>
<main class="page-content">

<?php if ($flash_msg): ?>
<div class="alert alert-<?= $flash_type ?> alert-auto-dismiss mb-3">
    <i class="fa-solid fa-<?= $flash_type==='success'?'circle-check':'circle-exclamation' ?> me-2"></i><?= e($flash_msg) ?>
</div>
<?php endif; ?>

<div class="card-panel">
    <div class="card-panel-header">
        <div class="card-panel-title">
            <div class="title-icon" style="background:var(--warning-light);color:var(--warning);"><i class="fa-solid fa-bell"></i></div>
            Notices &amp; Announcements
            <span class="badge" style="background:var(--warning-light);color:var(--warning);font-size:11px;padding:3px 8px;border-radius:20px;"><?= $total ?></span>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <div class="search-bar">
                    <i class="fa-solid fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Search notices..." value="<?= e($search) ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-search"></i> Search</button>
                <?php if ($search): ?>
                <a href="notices.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-xmark"></i></a>
                <?php endif; ?>
            </form>
            <button class="btn btn-sm" style="background:var(--warning);color:#fff;" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
                <i class="fa-solid fa-plus"></i> Add Notice
            </button>
        </div>
    </div>

    <div class="table-wrapper">
        <?php if (empty($records)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fa-solid fa-bell-slash"></i></div>
            <h6>No Notices Found</h6><p>Publish a notice using the button above.</p>
        </div>
        <?php else: ?>
        <table class="data-table">
            <thead>
                <tr><th>#</th><th>Notice ID</th><th>Title</th><th>Description</th><th>Publish Date</th><th>Attachment</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($records as $i => $r): ?>
                <?php $enc = urlencode(json_encode($r, JSON_HEX_QUOT|JSON_HEX_APOS)); ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;"><?= $offset+$i+1 ?></td>
                    <td class="td-id"><?= e($r['notice_id']) ?></td>
                    <td style="max-width:200px;"><div class="fw-600 text-truncate-2" style="font-size:13px;"><?= e($r['title']) ?></div></td>
                    <td style="max-width:260px;"><div class="text-truncate-2" style="font-size:12.5px;color:var(--text-muted);"><?= e($r['description']) ?></div></td>
                    <td>
                        <div style="font-weight:700;color:var(--warning);font-size:13px;"><?= formatDate($r['publish_date']) ?></div>
                    </td>
                    <td>
                        <?php if ($r['attachment']): ?>
                        <a href="uploads/notices/<?= urlencode($r['attachment']) ?>" target="_blank" class="btn-action btn-view" style="width:auto;padding:4px 10px;font-size:11px;">
                            <i class="fa-solid fa-paperclip me-1"></i>File
                        </a>
                        <?php else: echo '<span style="color:var(--text-muted);font-size:12px;">—</span>'; endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action btn-view" onclick="viewNotice('<?= $enc ?>')" title="View"><i class="fa-solid fa-eye"></i></button>
                            <button class="btn-action btn-edit" onclick="editNotice('<?= $enc ?>')" title="Edit"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action btn-delete" onclick="confirmDelete('notices.php?action=delete&id=<?= $r['id'] ?>','<?= addslashes(e($r['title'])) ?>')" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="pagination-wrapper">
        <div class="pagination-info">Showing <?= $offset+1 ?> – <?= min($offset+$per_page,$total) ?> of <?= $total ?> records</div>
        <nav><ul class="pagination mb-0">
            <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
            <?php for ($p=1;$p<=$total_pages;$p++): ?>
            <li class="page-item <?= $p==$page?'active':'' ?>"><a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"><?= $p ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $page>=$total_pages?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>

</main>
<?php include 'includes/footer.php'; ?>
</div>

<!-- ADD NOTICE MODAL -->
<div class="modal fade" id="addNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#e65100,#bf360c);">
                <h5 class="modal-title"><i class="fa-solid fa-bell me-2"></i>Add Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12"><label class="form-label">Notice Title <span class="req">*</span></label>
                            <input type="text" class="form-control" name="title" required placeholder="Brief title of the notice"></div>
                        <div class="col-md-6"><label class="form-label">Publish Date <span class="req">*</span></label>
                            <input type="date" class="form-control" name="publish_date" required value="<?= date('Y-m-d') ?>"></div>
                        <div class="col-12"><label class="form-label">Description <span class="req">*</span></label>
                            <textarea class="form-control" name="description" required rows="5" placeholder="Full notice content..."></textarea></div>
                        <div class="col-12"><label class="form-label">Attachment <span style="font-size:11px;color:var(--text-muted);">(PDF, DOC, JPG – max 5MB)</span></label>
                            <input type="file" class="form-control" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Cancel</button>
                    <button type="submit" class="btn btn-warning" style="color:#fff;"><i class="fa-solid fa-paper-plane"></i> Publish Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT NOTICE MODAL -->
<div class="modal fade" id="editNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="fa-solid fa-pen me-2"></i>Edit Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="existing_attachment" id="edit_existing_att">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Notice ID</label>
                            <input type="text" class="form-control" id="edit_notice_id" disabled></div>
                        <div class="col-12"><label class="form-label">Title <span class="req">*</span></label>
                            <input type="text" class="form-control" name="title" id="edit_title" required></div>
                        <div class="col-md-6"><label class="form-label">Publish Date <span class="req">*</span></label>
                            <input type="date" class="form-control" name="publish_date" id="edit_publish_date" required></div>
                        <div class="col-12"><label class="form-label">Description <span class="req">*</span></label>
                            <textarea class="form-control" name="description" id="edit_description" required rows="5"></textarea></div>
                        <div class="col-12"><label class="form-label">Replace Attachment</label>
                            <input type="file" class="form-control" name="attachment" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW NOTICE MODAL -->
<div class="modal fade" id="viewNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#e65100,#bf360c);">
                <h5 class="modal-title"><i class="fa-solid fa-bell me-2"></i>Notice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4"><div class="detail-row"><div class="detail-label">Notice ID</div><div class="detail-value td-id" id="vn_id"></div></div></div>
                    <div class="col-md-4"><div class="detail-row"><div class="detail-label">Publish Date</div><div class="detail-value fw-700" style="color:var(--warning);" id="vn_date"></div></div></div>
                    <div class="col-12"><div class="detail-row"><div class="detail-label">Title</div><div class="detail-value fw-700" id="vn_title"></div></div></div>
                    <div class="col-12"><hr class="detail-divider"><div class="detail-label">Description</div><div class="detail-value" id="vn_desc" style="color:var(--text-muted);line-height:1.8;white-space:pre-wrap;"></div></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> Close</button></div>
        </div>
    </div>
</div>

<script>
function editNotice(data) {
    const f = JSON.parse(decodeURIComponent(data));
    setVal('edit_id',f.id); setVal('edit_notice_id',f.notice_id);
    setVal('edit_title',f.title); setVal('edit_description',f.description);
    setVal('edit_publish_date',f.publish_date);
    document.getElementById('edit_existing_att').value = f.attachment || '';
    new bootstrap.Modal(document.getElementById('editNoticeModal')).show();
}
function viewNotice(data) {
    const f = JSON.parse(decodeURIComponent(data));
    setHtml('vn_id',f.notice_id); setHtml('vn_title',f.title);
    setHtml('vn_date',formatDate(f.publish_date));
    document.getElementById('vn_desc').textContent = f.description || '—';
    new bootstrap.Modal(document.getElementById('viewNoticeModal')).show();
}
</script>
