<?php
include 'db.php';
?>

<!-- ===== All Seekers ===== -->
<h5>All Seekers</h5>
<div class="table-responsive" style="overflow: visible !important;">
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Password</th>
        <th>View</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sicker_query = $conn->query("SELECT sicker_id, sicker_name, sicker_mobile, password, status FROM sickers ORDER BY sicker_id ASC");
      if ($sicker_query && $sicker_query->num_rows > 0) {
          while ($s = $sicker_query->fetch_assoc()) {
              $id = (int)$s['sicker_id'];
              $name = htmlspecialchars($s['sicker_name']);
              $mobile = htmlspecialchars($s['sicker_mobile']);
              $password = htmlspecialchars($s['password']);
              $status = htmlspecialchars($s['status']);
              echo "<tr>
                      <td>{$id}</td>
                      <td>{$name}</td>
                      <td>{$mobile}</td>
                      <td>{$password}</td>
                      <td><button type='button' class='btn btn-sm btn-info view-btn' data-url='view_sicker.php?id={$id}'>View</button></td>
                      <td>
                        <select class='form-select form-select-sm status-dropdown' data-id='{$id}' data-type='sicker'>
                          <option value='pending' ".($status=='pending'?'selected':'').">Pending</option>
                          <option value='active' ".($status=='active'?'selected':'').">Active</option>
                          <option value='inactive' ".($status=='inactive'?'selected':'').">Inactive</option>
                        </select>
                      </td>
                    </tr>";
          }
      } else {
          echo '<tr><td colspan="6" class="text-center">No sickers found</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<hr>

<!-- ===== All Providers ===== -->
<h5>All Providers</h5>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Password</th>
        <th>View</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $provider_query = $conn->query("SELECT provider_id, provider_name, provider_mobile, password, status FROM providers ORDER BY provider_id ASC");
      if ($provider_query && $provider_query->num_rows > 0) {
          while ($p = $provider_query->fetch_assoc()) {
              $id = (int)$p['provider_id'];
              $name = htmlspecialchars($p['provider_name']);
              $mobile = htmlspecialchars($p['provider_mobile']);
              $password = htmlspecialchars($p['password']);
              $status = htmlspecialchars($p['status']);
              echo "<tr>
                      <td>{$id}</td>
                      <td>{$name}</td>
                      <td>{$mobile}</td>
                      <td>{$password}</td>
                      <td><button type='button' class='btn btn-sm btn-info view-btn' data-url='view_provider.php?id={$id}'>View</button></td>
                      <td>
                        <select class='form-select form-select-sm status-dropdown' data-id='{$id}' data-type='provider'>
                          <option value='pending' ".($status=='pending'?'selected':'').">Pending</option>
                          <option value='active' ".($status=='active'?'selected':'').">Active</option>
                          <option value='inactive' ".($status=='inactive'?'selected':'').">Inactive</option>
                        </select>
                      </td>
                    </tr>";
          }
      } else {
          echo '<tr><td colspan="6" class="text-center">No providers found</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<!-- IFRAME container & loading overlay -->
<div class="mt-3" id="iframeContainer">
  <iframe id="viewFrame" style="display:none;width:100%;height:500px;border:1px solid #ccc;border-radius:6px;"></iframe>
</div>

<!-- Loading overlay (hidden by default) -->
<style>
#loadingOverlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.35);
  z-index: 9999;
  align-items: center;
  justify-content: center;
}
#loadingOverlay .spinner {
  background: #fff;
  padding: 18px 22px;
  border-radius: 10px;
  box-shadow: 0 6px 18px rgba(0,0,0,.2);
  display: flex;
  gap: 10px;
  align-items: center;
}
</style>

<div id="loadingOverlay">
  <div class="spinner">
    <div class="spinner-border" role="status" aria-hidden="true"></div>
    <div>Loading…</div>
  </div>
</div>

<!-- Unified JS: view + status -->
<script>
(function(){
  // helper: show/hide loading overlay
  function showLoading(show = true) {
    const overlay = document.getElementById('loadingOverlay');
    if (!overlay) return;
    overlay.style.display = show ? 'flex' : 'none';
  }

  // Click handler (event delegation) for view buttons
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('button.view-btn, button[data-url], button[onclick*="loadInIframe"]');
    if (!btn) return;

    e.preventDefault();

    // Try data-url first
    let url = btn.dataset && btn.dataset.url ? btn.dataset.url : null;

    // fallback to data-id + type (infer)
    if (!url && btn.dataset && btn.dataset.id) {
      const id = encodeURIComponent(btn.dataset.id);
      // try to find type from dataset or from same row's select
      let type = (btn.dataset && btn.dataset.type) ? btn.dataset.type : null;
      if (!type) {
        const row = btn.closest('tr');
        const sel = row ? row.querySelector('select[data-type]') : null;
        if (sel) type = sel.dataset.type;
      }
      if (!type) type = 'provider';
      url = (type === 'sicker') ? `view_sicker.php?id=${id}` : `view_provider.php?id=${id}`;
    }

    // fallback to inline onclick pattern: loadInIframe('url')
    if (!url) {
      const onclick = btn.getAttribute('onclick') || '';
      const m = onclick.match(/loadInIframe\(\s*['"]([^'"]+)['"]\s*\)/);
      if (m) url = m[1];
    }

    if (!url) {
      console.warn('View URL not found for button', btn);
      return;
    }

    // show iframe and load
    const iframe = document.getElementById('viewFrame');
    if (!iframe) {
      window.open(url, '_blank');
      return;
    }

    // optionally hide a dashboard section if present (original code referenced it)
    const dash = document.getElementById('dashboardSection');
    if (dash) dash.style.display = 'none';

    showLoading(true);
    iframe.style.display = 'block';
    iframe.src = url;
    // hide overlay when loaded
    iframe.onload = function() {
      showLoading(false);
    };
    // scroll into view
    setTimeout(()=> iframe.scrollIntoView({ behavior: 'smooth' }), 80);
  });

  // Change handler for status selects (event delegation)
  document.addEventListener('change', function(e) {
    const sel = e.target.closest('select.status-dropdown, select[data-id][data-type], select[onchange*="updateStatus"]');
    if (!sel) return;

    const id = sel.dataset.id || sel.getAttribute('data-id');
    const type = sel.dataset.type || sel.getAttribute('data-type');
    const status = sel.value;

    if (!id || !type || !status) {
      alert('Missing parameters for status update (data-id / data-type).');
      return;
    }

    sel.disabled = true;
    sel.classList.remove('is-valid','is-invalid');

    (async function() {
      try {
        const params = new URLSearchParams({ id, type, status });
        const resp = await fetch('update.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          body: params.toString()
        });

        const text = (await resp.text()).trim();
        console.log('update.php response:', text);

        let ok = false;
        try {
          const j = JSON.parse(text);
          if (j && (j.success === true || j.status === 'success' || j.result === 'success')) ok = true;
        } catch (err) {
          // not JSON
        }
        if (text === 'success' || text === '1' || /success/i.test(text)) ok = true;

        if (ok) {
          sel.classList.add('is-valid');
          setTimeout(()=> sel.classList.remove('is-valid'), 1200);
        } else {
          sel.classList.add('is-invalid');
          alert('Failed to update status. Server response: ' + text);
        }
      } catch (err) {
        console.error(err);
        sel.classList.add('is-invalid');
        alert('Error updating status: ' + err.message);
      } finally {
        sel.disabled = false;
      }
    })();
  });

})();
</script>
