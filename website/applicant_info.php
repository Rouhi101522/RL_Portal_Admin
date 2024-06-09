<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RealLife Application Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  </head>

  <body>

    <div class="header">
      <div class="row">
        <nav class="navbar navbar-dark fixed-top" style="background-color: #71bf43; height: 5em;">
          <div class="container-fluid">
            <a class="navbar-brand" href="#">
              <img src="assets\rl\Logo\Real LIFE Logo black.png" alt="Logo" style="width: 5cm; ">
            </a>
          </div>
        </nav>  
      </div>
    </div>

    <div class="container mt-5" style="padding-top: 5%;">
  <h2 class="mb-4">Applicant Information</h2>

  <form action="/submit" method="post">
    <div class="mb-3">
      <label for="name" class="form-label">Name:</label>
      <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="age" class="form-label">Age:</label>
      <input type="number" id="age" name="age" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="schoolYear" class="form-label">School Year:</label>
      <input type="text" id="schoolYear" name="schoolYear" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="address" class="form-label">Address:</label>
      <textarea id="address" name="address" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
      <label for="contactNumber" class="form-label">Contact Number:</label>
      <input type="tel" id="contactNumber" name="contactNumber" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  
  </body>
</html>