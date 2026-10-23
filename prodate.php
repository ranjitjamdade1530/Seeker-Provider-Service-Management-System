<!DOCTYPE html>
<html lang="en">
<head>
 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
  
<div class="container mt-4">
  <div class="card shadow border-0 mx-auto" style="max-width: 500px;">
    <div class="card-body">
      <h5 class="card-title text-center text-primary mb-4">
        <i class="bi bi-calendar-day"></i> Daily Request Report (Provider)
      </h5>
      <form action="provider_requests_by_date.php" method="POST" target="_blank">
        <div class="mb-3">
          <label for="report_date" class="form-label">Select Date</label>
          <input type="date" name="report_date" id="report_date" class="form-control" required>
        </div>
        <div class="text-end">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-download"></i> Download PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>