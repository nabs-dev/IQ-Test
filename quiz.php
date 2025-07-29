<?php
// Start session to track user progress
session_start();

// Include database connection
require_once 'db.php';

// Initialize or reset the quiz if starting new
if (!isset($_SESSION['quiz_started']) || isset($_GET['restart'])) {
    $_SESSION['quiz_started'] = true;
    $_SESSION['current_question'] = 0;
    $_SESSION['answers'] = [];
    $_SESSION['total_questions'] = 10; // Set the total number of questions
}

// Get current question index
$current = $_SESSION['current_question'];

// Process answer submission
if (isset($_POST['submit_answer'])) {
    if (isset($_POST['answer'])) {
        $_SESSION['answers'][$current] = $_POST['answer'];
    }
    
    // Move to next question
    $_SESSION['current_question']++;
    
    // If all questions answered, calculate result
    if ($_SESSION['current_question'] >= $_SESSION['total_questions']) {
        // Redirect to results page using JavaScript
        echo "<script>window.location.href = 'results.php';</script>";
        exit;
    }
    
    // Update current after processing
    $current = $_SESSION['current_question'];
}

// Fetch current question and options
try {
    // Get question
    $current_int = (int)$current; // Cast to integer for safety
    $stmt = $pdo->prepare("SELECT * FROM questions ORDER BY id LIMIT 1 OFFSET $current_int");
    $stmt->execute();
    $question = $stmt->fetch();
    
    if (!$question) {
        // If no question found, redirect to results
        echo "<script>window.location.href = 'results.php';</script>";
        exit;
    }
    
    // Get options for this question
    $stmt = $pdo->prepare("SELECT * FROM options WHERE question_id = ?");
    $stmt->execute([$question['id']]);
    $options = $stmt->fetchAll();
    
} catch(PDOException $e) {
    die("Error fetching question: " . $e->getMessage());
}

// Calculate progress percentage
$progress = ($current / $_SESSION['total_questions']) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQ Test - Question <?php echo $current + 1; ?></title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #333;
        }
        
        .container {
            width: 90%;
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        /* Header styles */
        header {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #4a6cf7;
            text-align: center;
        }
        
        .logo span {
            color: #333;
        }
        
        /* Quiz container */
        .quiz-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 40px;
        }
        
        .quiz-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .quiz-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .quiz-header p {
            color: #666;
            font-size: 16px;
        }
        
        /* Progress bar */
        .progress-container {
            width: 100%;
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin: 20px 0 40px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4a6cf7, #6a8eff);
            border-radius: 5px;
            transition: width 0.3s ease;
        }
        
        /* Question styles */
        .question {
            margin-bottom: 30px;
        }
        
        .question h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
            line-height: 1.4;
        }
        
        /* Options styles */
        .options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .option-label {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .option-label:hover {
            border-color: #4a6cf7;
            background-color: #f0f4ff;
        }
        
        .option-input {
            display: none;
        }
        
        .option-input:checked + .option-label {
            border-color: #4a6cf7;
            background-color: #f0f4ff;
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.15);
        }
        
        .option-text {
            margin-left: 15px;
            font-size: 16px;
            color: #333;
        }
        
        .option-marker {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #4a6cf7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .option-input:checked + .option-label .option-marker {
            background-color: #4a6cf7;
        }
        
        .option-input:checked + .option-label .option-marker:after {
            content: '';
            width: 10px;
            height: 10px;
            background-color: white;
            border-radius: 50%;
        }
        
        /* Button styles */
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .btn {
            display: inline-block;
            background-color: #4a6cf7;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.3);
        }
        
        .btn:hover {
            background-color: #3a5bd9;
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(74, 108, 247, 0.4);
        }
        
        .btn-secondary {
            background-color: #e0e0e0;
            color: #333;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary:hover {
            background-color: #d0d0d0;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
        
        /* Timer styles */
        .timer {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            color: #666;
        }
        
        .timer span {
            font-weight: bold;
            color: #4a6cf7;
        }
        
        /* Footer */
        footer {
            background-color: #fff;
            padding: 20px 0;
            margin-top: auto;
            text-align: center;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .footer-text {
            color: #888;
            font-size: 14px;0,0,0.05);
        }
        
        .footer-text {
            color: #888;
            font-size: 14px;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .quiz-container {
                padding: 30px 20px;
            }
            
            .question h2 {
                font-size: 20px;
            }
            
            .option-label {
                padding: 12px 15px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">IQ<span>Test</span></div>
        </div>
    </header>
    
    <main class="container">
        <div class="quiz-container">
            <div class="quiz-header">
                <h1>IQ Test Question <?php echo $current + 1; ?> of <?php echo $_SESSION['total_questions']; ?></h1>
                <p>Select the best answer for each question. Take your time and think carefully.</p>
            </div>
            
            <div class="progress-container">
                <div class="progress-bar" style="width: <?php echo $progress; ?>%"></div>
            </div>
            
            <div class="timer">
                Time remaining: <span id="time">00:30</span>
            </div>
            
            <div class="question">
                <h2><?php echo htmlspecialchars($question['question_text']); ?></h2>
            </div>
            
            <form method="post" id="quiz-form">
                <div class="options">
                    <?php foreach ($options as $index => $option): ?>
                        <input type="radio" name="answer" id="option<?php echo $index; ?>" value="<?php echo $option['id']; ?>" class="option-input" <?php echo (isset($_SESSION['answers'][$current]) && $_SESSION['answers'][$current] == $option['id']) ? 'checked' : ''; ?>>
                        <label for="option<?php echo $index; ?>" class="option-label">
                            <div class="option-marker"></div>
                            <div class="option-text"><?php echo htmlspecialchars($option['option_text']); ?></div>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <div class="btn-container">
                    <?php if ($current > 0): ?>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='quiz.php?prev=true'">Previous</button>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>
                    
                    <button type="submit" name="submit_answer" class="btn">Next Question</button>
                </div>
            </form>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p class="footer-text">&copy; <?php echo date('Y'); ?> IQTest. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Timer functionality
        let timeLeft = 30; // 30 seconds per question
        const timerElement = document.getElementById('time');
        
        const timer = setInterval(function() {
            timeLeft--;
            
            // Format time
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // If time runs out, submit the form
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById('quiz-form').submit();
            }
        }, 1000);
        
        // Form validation - ensure an option is selected
        document.getElementById('quiz-form').addEventListener('submit', function(e) {
            const options = document.querySelectorAll('input[name="answer"]');
            let selected = false;
            
            options.forEach(option => {
                if (option.checked) {
                    selected = true;
                }
            });
            
            if (!selected) {
                e.preventDefault();
                alert('Please select an answer before proceeding.');
            }
        });
    </script>
</body>
</html>
