
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db.php';

if (!isset($_SESSION['sicker_id'])) {
    header("Location: sicker_login.php");
    exit();
}
 
// Auto logout after 10 hours
if(!isset($_SESSION['login_time'])){
    $_SESSION['login_time'] = time();
} else {
    $elapsed = time() - $_SESSION['login_time'];
    if($elapsed > 36000){
        session_destroy();
        header("Location: sicker_login.php?msg=Session expired after 10 hours");
        exit();
    }
} 

$sicker_id = $_SESSION['sicker_id'];
$sicker_name = $_SESSION['sicker_name'] ?? "Sicker User";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Seeker Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body { min-height: 100vh; background-color: #f8f9fa; }
.sidebar { height: 100vh; background-color: #343a40; position: fixed; width: 250px; padding-top: 20px; }
.sidebar a { color: white; padding: 10px 20px; display: block; text-decoration: none; }
.sidebar a:hover, .sidebar a.active { background-color: #0d6efd; }
.main-content { margin-left: 250px; padding: 20px; }
.navbar { margin-left: 250px; }
</style>
</head>
<body>
 
<div class="sidebar"> 
  <h4 class="text-center text-white">Seeker Panel</h4>
  <a href="#" onclick="loadTab('dashboard_home', this)" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="#" onclick="loadTab('search_provider', this)"><i class="bi bi-search"></i> Search Provider</a>
  <a href="#" onclick="loadTab('profile', this)"><i class="bi bi-person-circle"></i> Profile</a>
  <a href="#" onclick="loadTab('user_requests', this)"><i class="bi bi-clock-history"></i> Request</a>
  <a href="#" onclick="loadTab('catalog', this)"><i class="bi bi-book"></i> Catalog</a>
  <a href="#" onclick="loadTab('sickerdate_report', this)"><i class="bi bi-calendar-day"></i> Day Report</a>
  <a href="#" onclick="loadTab('simonth', this)"><i class="bi bi-calendar-month"></i> Month Report</a>
  <a href="#" onclick="loadTab('siyear', this)"><i class="bi bi-calendar3"></i> Year Report</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <span class="navbar-brand">Welcome, <?= htmlspecialchars($sicker_name) ?></span>
  </div>
</nav> 

<div class="main-content">
  <div id="mainContent">
    <h2>Dashboard</h2>
    <p>Welcome to your dashboard. You can manage your profile, search providers, and view reports.</p>
  </div>
</div>

<script>
const sickerId = <?= json_encode($sicker_id) ?>;

// Load tab content via AJAX
function loadTab(tab, el) {
    $('.sidebar a').removeClass('active');
    if(el) $(el).addClass('active');

    $('#mainContent').html('<div class="text-muted">Loading...</div>');

    $.get(tab + '.php', function(html){
        $('#mainContent').html(html);
        attachHandlers();
    }).fail(function(){
        $('#mainContent').html('<div class="text-danger">Failed to load tab.</div>');
    });
}

// Attach handlers dynamically after content loads
function attachHandlers(){

    // ===== Profile Form AJAX Submit =====
    $('#profileForm').off('submit').on('submit', function(e){
        e.preventDefault();

        let formData = new FormData(this);
        formData.append('ajax', 1);

        $.ajax({
            url: 'profile.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(resp){
                if(typeof resp === 'string'){ 
                    try { resp = JSON.parse(resp); } catch(e) {}
                }
                if(resp.success){
                    alert(resp.message);
                    if(resp.name){
                        $('.navbar-brand').text("Welcome, " + resp.name);
                    }
                } else {
                    alert(resp.message || 'Update failed.');
                }
            },
            error: function(){
                alert('Error updating profile. Try again.');
            }
        });
    });

    // ===== Send request button =====
   // ===== Send request button =====
$('.send-request-btn').off('click').on('click', function(){
    const btn = $(this);

    // ✅ Check total hours before allowing request
    $.get('check_hours.php', function(resp){
        if(!resp.success){
            alert(resp.message);
            return; // block request
        } 

        // continue only if hours < 10
        const requestDate = $('#request_date').val() || btn.data('date');
        if(!requestDate){
            alert('Please select a date first!');
            return;
        }

        $.post('send_request.php', {
            sicker_id: sickerId,
            provider_id: btn.data('provider'),
            request_type: btn.data('service'),
            slot: btn.data('slot'),
            city: btn.data('city'),
            area: btn.data('area'),
            request_date: requestDate
        }, function(resp){
            if(resp.success){
                alert(resp.message);
                btn.prop('disabled', true).text('Requested');
            } else {
                alert(resp.message);
            }
        }, 'json');

    }, 'json');
});


    // ===== Rating fetch =====
    $('.rating-display').each(function(){
        const container = $(this);
        const provider_id = container.data('provider');
        const service_name = container.data('service');

        $.post('get_rating.php', {provider_id, request_type: service_name}, function(resp){
            container.text(resp.success ? resp.stars : 'No rating');
        }, 'json');
    });

    // ===== City → Area dynamic dropdown =====
    $('#city, #city_profile').off('change').on('change', function(){
        const city = $(this).val();
        const target = ($(this).attr('id') === 'city_profile') ? '#area_profile' : '#area';
        $(target).html('<option>Loading...</option>');
        $.post('area_fetch.php', {city}, function(res){
            if(res.success){
                let html = '<option value="">Select Area</option>';
                res.areas.forEach(a => html += `<option value="${a}">${a}</option>`);
                $(target).html(html);
            } else {
                $(target).html('<option>No areas</option>');
            }
        }, 'json');
    });

    // ===== Rating Change + Save Time Spent =====
 $(document).ready(function(){
    $(".btn-save").off("click").on("click", function(){
        const requestId = $(this).data("id");
        const row = $(this).closest("tr");
        const rating = row.find(".rating-field").val();
        const timeSpent = row.find(".time-field").val();

        if(!rating || !timeSpent){
            alert("⚠️ Please select a rating and enter time spent.");
            return;
        }  
  
        $.post("update_rating.php", 
            { request_id: requestId, rating: rating, time_spent: timeSpent }, 
            function(resp){
                if(resp.success){
                    alert("✅ " + resp.message);

                    // 🔒 disable fields after save
                    row.find(".rating-field").prop("disabled", true);
                    row.find(".time-field").prop("disabled", true);
                    row.find(".btn-save")
                       .prop("disabled", true)
                       .removeClass("btn-success")
                       .addClass("btn-secondary")
                       .text("Saved");
                } else {
                    alert("❌ " + resp.message);
                }
            }, 
        "json");
    });
});


}
</script>
 
</body>
</html>
