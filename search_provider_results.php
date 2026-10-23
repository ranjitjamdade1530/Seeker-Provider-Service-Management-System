<?php
session_start();
include 'db.php';

$sicker_id = $_SESSION['sicker_id'] ?? 0;

$city = $_POST['city'] ?? '';
$area = $_POST['area'] ?? '';
$service = $_POST['service_name'] ?? '';
$slot = $_POST['time_slot'] ?? '';
$date = $_POST['request_date'] ?? '';

if(!$city || !$area || !$service || !$slot || !$date){
    exit('<div class="alert alert-warning">All fields are required.</div>');
}

$stmt = $conn->prepare("
    SELECT p.provider_id, p.provider_name, p.provider_mobile, p.city, p.area, p.provider_gender, p.provider_age,
           pr.service_name, pr.time_slot, pr.available_date
    FROM providers p
    INNER JOIN provider_requests pr ON p.provider_id = pr.provider_id
    WHERE p.city=? AND p.area=? AND pr.service_name=? AND pr.time_slot=? AND pr.available_date=?
");
$stmt->bind_param("sssss",$city,$area,$service,$slot,$date);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    echo '<div class="alert alert-warning">No providers found for your selection.</div>';
    exit;
}
?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Mobile</th><th>City</th><th>Area</th><th>Gender/Age</th>
            <th>Service</th><th>Slot</th><th>Date</th><th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $row['provider_id'] ?></td>
            <td><?= $row['provider_name'] ?></td>
            <td><?= $row['provider_mobile'] ?></td>
            <td><?= $row['city'] ?></td>
            <td><?= $row['area'] ?></td>
            <td><?= $row['provider_gender'] ?>/<?= $row['provider_age'] ?></td>
            <td><?= $row['service_name'] ?></td>
            <td><?= $row['time_slot'] ?></td>
            <td><?= $row['available_date'] ?></td>
            <td>
                <button class="btn btn-sm btn-success send-request-btn"
                        data-provider="<?= $row['provider_id'] ?>"
                        data-service="<?= $row['service_name'] ?>"
                        data-slot="<?= $row['time_slot'] ?>"
                        data-city="<?= $row['city'] ?>"
                        data-area="<?= $row['area'] ?>"
                        data-date="<?= $row['available_date'] ?>">
                    Send Request
                </button>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<script>
const sickerId = <?= json_encode($sicker_id) ?>;

$('.send-request-btn').on('click', function(){
    const btn = $(this);
    $.post('send_request.php', {
        sicker_id: sickerId,
        provider_id: btn.data('provider'),
        request_type: btn.data('service'),
        slot: btn.data('slot'),
        city: btn.data('city'),
        area: btn.data('area'),
        request_date: btn.data('date')
    }, function(resp){
        alert(resp.message);
        if(resp.success){
            btn.prop('disabled', true).text('Requested');
        }
    }, 'json');
});
</script>
