<?php
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_emergency'])) {
    $reason = trim($_POST['reason']);
    $file = $_FILES['certificate'];

    if (empty($reason)) {
        $error = "Reason is required.";
    } elseif (empty($file['name'])) {
        $error = "Please upload a certificate.";
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $error = "Invalid file format. Allowed: PDF, JPG, PNG.";
        } else {
            $folder = 'uploads/';
            if (!is_dir($folder)) mkdir($folder, 0777, true);
            $filename = 'cert_' . $provider_id . '_' . time() . '.' . $ext;
            $path = $folder . $filename;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                $stmt = $conn->prepare("INSERT INTO emergency_conversions (provider_id, reason, certificate_path, request_date) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $provider_id, $reason, $path);
                $stmt->execute();

                $success = "Request sent successfully!";
            } else {
                $error = "Failed to upload certificate.";
            }
        }
    }
}
?>

<div class="card shadow p-4">
    <h4 class="mb-4">Emergency Medical Conversion (Provider → Sicker)</h4>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Reason for Emergency Conversion</label>
            <textarea name="reason" rows="3" class="form-control" required placeholder="Explain your medical emergency..."></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Medical Certificate</label>
            <input type="file" name="certificate" accept=".pdf,.jpg,.jpeg,.png" class="form-control" required>
            <small class="text-muted">Allowed formats: PDF, JPG, PNG</small>
        </div>
        <button type="submit" name="submit_emergency" class="btn btn-danger px-4">Send Request</button>
    </form>
</div>
