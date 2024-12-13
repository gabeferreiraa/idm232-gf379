<?php
// Database connection
require_once './db.php';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - Fork & Flavor</title>
    <link rel="stylesheet" href="./index.css">
</head>
<body>
    

    <!-- Main Content -->
    <main class="help-container container">
        <section class="help-section">
            <h2>Frequently Asked Questions (FAQs)</h2>
            <div class="faq">
                <h3>How do I search for recipes?</h3>
                <p>Use the search bar on the home page to find your favorite recipes by name, cuisine, or ingredients.</p>

                <h3>How can I submit my own recipe?</h3>
                <p>Currently, recipe submissions are not enabled. Please contact us if you have suggestions or feedback.</p>

                <h3>How do I contact support?</h3>
                <p>You can reach out to us using the contact form below or email us directly at support@forkandflavor.com.</p>
            </div>
        </section>

        <section class="help-section">
            <h2>Contact Us</h2>
            <?php
            // Handle form submission
            $success = '';
            $error = '';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Retrieve form data
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $message = trim($_POST['message']);

                // Basic validation
                if (empty($name) || empty($email) || empty($message)) {
                    $error = "All fields are required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid email format.";
                } else {
                    // Prepare email
                    $to = "support@forkandflavor.com"; // Replace with your support email
                    $subject = "Contact Form Submission from " . $name;
                    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
                    $headers = "From: $email";

                    // Send email
                    if (mail($to, $subject, $body, $headers)) {
                        $success = "Your message has been sent successfully!";
                        // Clear form fields
                        $name = $email = $message = '';
                    } else {
                        $error = "There was an error sending your message. Please try again later.";
                    }
                }
            }
            ?>

            <?php if (!empty($success)): ?>
                <p class="success-message"><?php echo $success; ?></p>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>

            <form class="contact-form" action="help.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>

                <button type="submit">Send Message</button>
            </form>
        </section>
    </main>
</body>
</html>