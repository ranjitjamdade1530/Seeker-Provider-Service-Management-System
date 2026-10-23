<?php
session_start();
require 'db.php';
 
if(isset($_GET['type'], $_GET['id'], $_GET['status'])){
    $type = $_GET['type']; // sicker or provider
    $id = intval($_GET['id']);
    $status = $_GET['status'];

    if(!in_array($status, ['active','pending','rejected'])){
        die("Invalid status");
    }

    if($type === 'sicker'){
        $stmt = $conn->prepare("UPDATE sickers SET status=? WHERE sicker_id=?");
    } elseif($type === 'provider'){
        $stmt = $conn->prepare("UPDATE providers SET status=? WHERE provider_id=?");
    } else {
        die("Invalid type");
    }

    $stmt->bind_param("si", $status, $id);
    if($stmt->execute()){
        header("Location: admin_dashboard.php?msg=Status updated successfully");
    } else {
        echo "Error updating status.";
    }
}
?>
<?php
$sicker_query = $conn->query("SELECT sicker_id, sicker_name, sicker_mobile, password, status FROM sickers ORDER BY sicker_id ASC");
if($sicker_query && $sicker_query->num_rows > 0){
    while($s = $sicker_query->fetch_assoc()){
        echo "<tr>
                <td>" . htmlspecialchars($s['sicker_id']) . "</td>
                <td>" . htmlspecialchars($s['sicker_name']) . "</td>
                <td>" . htmlspecialchars($s['sicker_mobile']) . "</td>
                <td>" . htmlspecialchars($s['password']) . "</td>
                <td>" . htmlspecialchars($s['status']) . "</td>
                <td>
                    <a href='update_status.php?type=sicker&id=" . $s['sicker_id'] . "&status=active' class='btn btn-success btn-sm'>Activate</a>
                    <a href='update_status.php?type=sicker&id=" . $s['sicker_id'] . "&status=rejected' class='btn btn-danger btn-sm'>Reject</a>
                </td>
              </tr>";
    }
} else {
    echo '<tr><td colspan="6" class="text-center">No sickers found</td></tr>';
}
//provider
$provider_query = $conn->query("SELECT provider_id, provider_name, provider_mobile, password, status FROM providers ORDER BY provider_id ASC");
if($provider_query && $provider_query->num_rows > 0){
    while($p = $provider_query->fetch_assoc()){
        echo "<tr>
                <td>" . htmlspecialchars($p['provider_id']) . "</td>
                <td>" . htmlspecialchars($p['provider_name']) . "</td>
                <td>" . htmlspecialchars($p['provider_mobile']) . "</td>
                <td>" . htmlspecialchars($p['password']) . "</td>
                <td>" . htmlspecialchars($p['status']) . "</td>
                <td>
                    <a href='update_status.php?type=provider&id=" . $p['provider_id'] . "&status=active' class='btn btn-success btn-sm'>Activate</a>
                    <a href='update_status.php?type=provider&id=" . $p['provider_id'] . "&status=rejected' class='btn btn-danger btn-sm'>Reject</a>
                </td>
              </tr>";
    }
} else {
    echo '<tr><td colspan="6" class="text-center">No providers found</td></tr>';
}
?>