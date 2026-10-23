<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-body">
      <h5 class="card-title text-center text-success mb-4">
        <i class="bi bi-calendar-year"></i> Yearly Request Report 
      </h5>
      <div class="row mb-3">
        <div class="col-md-4">
          <input type="number" id="year" class="form-control" placeholder="e.g. 2025" min="2000" max="2099">
        </div>
        <div class="col-md-2">
          <button id="generateReport" class="btn btn-primary">
            <i class="bi bi-search"></i> Generate Report
          </button>
        </div>
      </div>
      <div id="reportResult"></div>
    </div>
  </div>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
$(document).ready(function(){
    $('#generateReport').click(function(){
        var year = $('#year').val();
        if(year == '') { alert('Please enter year'); return; }

        $.ajax({
            url: 'sicker_yearly_requests_dashboard.php',
            type: 'GET',
            data: { year: year },
            success: function(data){
                $('#reportResult').html(data);
            },
            error: function(){
                $('#reportResult').html('<div class="alert alert-danger">Error fetching data.</div>');
            }
        });
    });
});
</script>
