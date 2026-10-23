<?php
session_start();
include 'db.php';

if (!isset($_SESSION['sicker_id'])) {
    header("Location: sicker_login.php");
    exit;
}

$sicker_id = $_SESSION['sicker_id'];
$sicker_name = $_SESSION['sicker_name'] ?? "Sicker User";

// Fetch dropdown data
$cities = $conn->query("SELECT city_name FROM cities ORDER BY city_name");
$services = $conn->query("SELECT service_name FROM request_services ORDER BY service_name");
$slots = $conn->query("SELECT time_range FROM time_slots ORDER BY time_range");

// Get search parameters
$selected_city = $_GET['city'] ?? '';
$selected_area = $_GET['area'] ?? '';
$selected_service = $_GET['service_name'] ?? '';
$selected_slot = $_GET['time_slot'] ?? '';
$selected_date = $_GET['request_date'] ?? '';

$results = [];

if (!empty($selected_city) && !empty($selected_area) && !empty($selected_service) && !empty($selected_slot) && !empty($selected_date)) {
    $stmt = $conn->prepare("
        SELECT 
            p.provider_id, 
            p.provider_name, 
            p.provider_mobile, 
            p.provider_gender, 
            pr.city, 
            pr.area, 
            pr.service_name, 
            pr.time_slot, 
            pr.available_date AS request_date
        FROM providers p
        INNER JOIN provider_requests pr ON p.provider_id = pr.provider_id
        WHERE pr.city = ? 
          AND pr.area = ? 
          AND pr.service_name = ? 
          AND pr.time_slot = ? 
          AND pr.available_date = ?
    ");
    $stmt->bind_param("sssss", $selected_city, $selected_area, $selected_service, $selected_slot, $selected_date);
    $stmt->execute();
    $results = $stmt->get_result();

    // Add priority sorting here
    $provider_list = [];

    while ($row = $results->fetch_assoc()) {
        $priority = 0;
        $provider_id = $row['provider_id'];

        // 1. Converted from seeker
        $check = $conn->query("SELECT sicker_id FROM sickers WHERE provider_id = '$provider_id'");
        $is_converted = ($check && $check->num_rows > 0) ? 1 : 0;
        if ($is_converted) $priority += 100;
        $row['was_seeker'] = $is_converted;

        // 2. Total time from request table
        $time_q = $conn->query("SELECT SUM(time_spent) AS total_time FROM request WHERE provider_id = '$provider_id'");
        $total_time = 0;
        if ($time_q && $time_row = $time_q->fetch_assoc()) {
            $total_time = (int)$time_row['total_time'];
        }
        $priority += $total_time;
        $row['total_time'] = $total_time;

        // 3. Average rating
        $service = $conn->real_escape_string($row['service_name']);
        $rating_q = $conn->query("SELECT AVG(rating) AS avg_rating FROM provider_ratings WHERE provider_id = '$provider_id' AND service_name = '$service'");
        $avg_rating = 0;
        if ($rating_q && $rating_row = $rating_q->fetch_assoc()) {
            $avg_rating = round((float)$rating_row['avg_rating'], 2);
        }
        $priority += $avg_rating * 10;
        $row['avg_rating'] = $avg_rating;

        $row['priority_score'] = $priority;
        $provider_list[] = $row;
    }

    // Sort by priority
    usort($provider_list, function ($a, $b) {
        return $b['priority_score'] <=> $a['priority_score'];
    });
}
?>
<div class="container mt-3">
<form id="searchForm" class="card p-4 shadow mb-4">
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Request Date</label>
            <input type="date" name="request_date" id="request_date" class="form-control"
                   min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($selected_date) ?>" required>
        </div>
        <div class="col-md-3">
            <label>City</label> 
            <select name="city" id="city" class="form-select" required>
                <option value="">Select City</option>
                <?php while($c = $cities->fetch_assoc()): ?>
                    <option value="<?= $c['city_name'] ?>" <?= ($selected_city==$c['city_name'])?'selected':'' ?>><?= $c['city_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label>Area</label>
            <select name="area" id="area" class="form-select" required>
                <option value="">Select Area</option>
                <?php
                if($selected_city){
                    $stmt2 = $conn->prepare("SELECT area_name FROM areas WHERE city_name=? ORDER BY area_name");
                    $stmt2->bind_param("s",$selected_city);
                    $stmt2->execute();
                    $res2 = $stmt2->get_result();
                    while($a = $res2->fetch_assoc()){
                        echo '<option value="'.$a['area_name'].'"'.($selected_area==$a['area_name']?' selected':'').'>'.$a['area_name'].'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label>Service</label>
            <select name="service_name" id="service_name" class="form-select" required>
                <option value="">Select Service</option>
                <?php while($s = $services->fetch_assoc()): ?>
                    <option value="<?= $s['service_name'] ?>" <?= ($selected_service==$s['service_name'])?'selected':'' ?>><?= $s['service_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3 mt-3">
            <label>Time Slot</label>
            <select name="time_slot" id="time_slot" class="form-select" required>
                <option value="">Select Slot</option>
                <?php while($t = $slots->fetch_assoc()): ?>
                    <option value="<?= $t['time_range'] ?>" <?= ($selected_slot==$t['time_range'])?'selected':'' ?>><?= $t['time_range'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
    <div class="text-center mt-3">
        <button type="submit" class="btn btn-primary px-5">Search</button>
    </div>
</form>

<div id="results">
<?php if(!empty($provider_list) && count($provider_list) > 0): ?>
    <div class="card p-3 shadow">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Mobile</th><th>City</th><th>Area</th>
                    <th>Gender</th><th>Service</th><th>Time Slot</th><th>Date</th>
                    <th>Note</th><th>Rating</th><th>Request</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($provider_list as $row): ?>
                <tr>
                    <td><?= $row['provider_id'] ?></td>
                    <td><?= $row['provider_name'] ?></td>
                    <td><?= $row['provider_mobile'] ?></td>
                    <td><?= $row['city'] ?></td>
                    <td><?= $row['area'] ?></td>
                    <td><?= $row['provider_gender']?></td>
                    <td><?= $row['service_name'] ?></td>
                    <td><?= $row['time_slot'] ?></td>
                    <td><?= $row['request_date'] ?></td>

                    <!-- Note Field -->
                    <td>
                        <textarea class="form-control note-field" rows="1" 
                                  data-provider="<?= $row['provider_id'] ?>"
                                  maxlength="1000" 
                                  placeholder="Max 100 words"></textarea>
                    </td>

                    <!-- Rating -->
                    <td class="rating-display" data-provider="<?= $row['provider_id'] ?>" data-service="<?= $row['service_name'] ?>"></td>

                    <!-- Send Request -->
                    <td>
                        <button type="button" class="btn btn-sm btn-success send-request-btn"
                            data-provider="<?= $row['provider_id'] ?>"
                            data-service="<?= $row['service_name'] ?>"
                            data-slot="<?= $row['time_slot'] ?>"
                            data-city="<?= $row['city'] ?>"
                            data-area="<?= $row['area'] ?>"
                            data-date="<?= $row['request_date'] ?>">
                            Send
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php elseif(!empty($_GET['search'])): ?>
    <div class="alert alert-warning">Providers are not allowed as per your interest...!</div>
<?php endif; ?>
</div>

<script>
function fetchResults(params){
    $.get('search_provider.php', params, function(data){
        $('#mainContent').html(data);
    });
}

$('#searchForm').on('submit', function(e){
    e.preventDefault();
    const params = {
        search:1,
        city:$('#city').val(),
        area:$('#area').val(),
        service_name:$('#service_name').val(),
        time_slot:$('#time_slot').val(),
        request_date:$('#request_date').val()
    };
    fetchResults(params);
});

// Dynamic area dropdown
$('#city').on('change', function(){
    const city = $(this).val();
    $('#area').html('<option>Loading...</option>');
    $.post('area_fetch.php', {city}, function(res){
        if(res.success){
            let html='<option value="">Select Area</option>';
            res.areas.forEach(a=> html+=`<option value="${a}">${a}</option>`);
            $('#area').html(html);
        } else {
            $('#area').html('<option>No areas</option>');
        }
    }, 'json');
});

// Send request with note
$(document).on('click', '.send-request-btn', function(){
    const btn=$(this);
    const sickerId=<?= json_encode($sicker_id) ?>;
    const requestDate=btn.data('date') || $('#request_date').val();
    const providerId=btn.data('provider');
    const note=$(`.note-field[data-provider="${providerId}"]`).val();

    if(!requestDate){ alert('Select date first'); return; }

    const words = note.trim().split(/\s+/).filter(Boolean).length;
    if(words > 100){
        alert("Note cannot exceed 100 words!");
        return;
    }

    $.post('send_request.php', {
        sicker_id:sickerId,
        provider_id:providerId,
        request_type:btn.data('service'),
        slot:btn.data('slot'),
        city:btn.data('city'),
        area:btn.data('area'),
        request_date:requestDate,
        note:note
    }, function(resp){
        alert(resp.message);
        if(resp.success){ btn.prop('disabled',true).text('Requested'); }
    }, 'json');
});

// Load ratings
$(document).ready(function(){
    $('.rating-display').each(function(){
        const container=$(this);
        $.post('get_rating.php',{
            provider_id: container.data('provider'),
            request_type: container.data('service')
        }, function(resp){
            container.text(resp.success?resp.stars:'No rating');
        }, 'json');
    });
});
</script>
