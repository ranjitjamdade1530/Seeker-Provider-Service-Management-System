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
   

<div class="container mt-5">
  <div class="card shadow border-0 mx-auto" style="max-width: 600px;">
    <div class="card-body">
      <h5 class="card-title text-center text-primary mb-4">
        <i class="bi bi-calendar-month-fill"></i> Monthly Request Report 
      </h5>
      <form action="provider_requests_by_month.php" method="POST" target="_blank">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="month" class="form-label">Select Month</label>
            <select name="month" id="month" class="form-select" required>
              <option value="">-- Select Month --</option>
              <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>"><?= date("F", mktime(0, 0, 0, $i, 1)) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="year" class="form-label">Enter Year</label>
            <input type="number" name="year" id="year" class="form-control" placeholder="e.g. 2025" min="2000" max="2099" required>
          </div>
        </div>
        <div class="text-end mt-4">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-download"></i> Download PDF
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


</body>
</html>