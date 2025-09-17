<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Loan - QuickLoan</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/quickloan_logo.png" alt="QuickLoan Logo">
        </div>
    </header>
    <section class="apply-form">
        <h2>Apply for Loan</h2>
        <form action="submit_application.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="contact">Contact Number:</label>
            <input type="tel" id="contact" name="contact" required>

            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>

            <label for="loan_type">Type of Loan:</label>
            <select id="loan_type" name="loan_type" required>
                <option value="Home Loan">Home Loan</option>
                <option value="Gold Loan">Gold Loan</option>
                <option value="Vehicle Loan">Vehicle Loan</option>
                <option value="Personal Loan">Personal Loan</option>
            </select>

            <div class="form-buttons">
                <button type="submit" class="submit-btn">Submit</button>
                <button type="reset" class="reset-btn">Reset</button>
            </div>
        </form>
    </section>
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            alert('Application submitted successfully. Our representative will contact you shortly.');
        });
    </script>
</body>
</html>