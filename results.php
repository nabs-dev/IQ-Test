<?php
// Start session to access quiz data
session_start();

// Include database connection
require_once 'db.php';

// Redirect if quiz not completed
if (!isset($_SESSION['quiz_started']) || !isset($_SESSION['answers']) || count($_SESSION['answers']) < $_SESSION['total_questions']) {
    header('Location: index.php');
    exit;
}

// Calculate score
$score = 0;
$correctAnswers = [];
$userAnswers = [];

try {
    // Get all questions with their correct answers
    $stmt = $pdo->query("SELECT q.id as question_id, q.question_text, o.id as option_id, o.option_text, o.is_correct 
                         FROM questions q 
                         JOIN options o ON q.id = o.question_id 
                         ORDER BY q.id");
    $results = $stmt->fetchAll();
    
    // Organize results by question
    $questions = [];
    foreach ($results as $row) {
        if (!isset($questions[$row['question_id']])) {
            $questions[$row['question_id']] = [
                'question_text' => $row['question_text'],
                'options' => [],
                'correct_option_id' => null
            ];
        }
        
        $questions[$row['question_id']]['options'][$row['option_id']] = $row['option_text'];
        
        if ($row['is_correct']) {
            $questions[$row['question_id']]['correct_option_id'] = $row['option_id'];
        }
    }
    
    // Calculate score
    foreach ($_SESSION['answers'] as $questionIndex => $selectedOptionId) {
        $questionId = $questionIndex + 1; // Assuming question IDs start from 1
        
        if (isset($questions[$questionId])) {
            $correctOptionId = $questions[$questionId]['correct_option_id'];
            
            // Store user and correct answers for display
            $userAnswers[$questionId] = [
                'question_text' => $questions[$questionId]['question_text'],
                'selected_option' => $questions[$questionId]['options'][$selectedOptionId] ?? 'No answer',
                'is_correct' => ($selectedOptionId == $correctOptionId)
            ];
            
            $correctAnswers[$questionId] = [
                'option_id' => $correctOptionId,
                'option_text' => $questions[$questionId]['options'][$correctOptionId] ?? 'Unknown'
            ];
            
            // Increment score if correct
            if ($selectedOptionId == $correctOptionId) {
                $score++;
            }
        }
    }
    
    // Get result interpretation based on score
    $stmt = $pdo->prepare("SELECT * FROM result_ranges WHERE min_score <= ? AND max_score >= ?");
    $stmt->execute([$score, $score]);
    $resultInterpretation = $stmt->fetch();
    
    // Save result to database
    $stmt = $pdo->prepare("INSERT INTO user_results (score) VALUES (?)");
    $stmt->execute([$score]);
    
} catch(PDOException $e) {
    die("Error calculating results: " . $e->getMessage());
}

// Calculate IQ score (simplified formula for demonstration)
$baseIQ = 100; // Average IQ
$scorePercentage = ($score / $_SESSION['total_questions']) * 100;
$iqScore = round($baseIQ + (($scorePercentage - 50) * 1.5));

// Ensure IQ score is within reasonable bounds
$iqScore = max(70, min(145, $iqScore));

// Clear session data for a new test
$_SESSION['quiz_completed'] = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your IQ Test Results</title>
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
            max-width: 900px;
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
        
        /* Results container */
        .results-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 40px;
        }
        
        .results-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .results-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .results-header p {
            color: #666;
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Score display */
        .score-display {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 40px 0;
        }
        
        .score-circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4a6cf7, #6a8eff);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            box-shadow: 0 8px 24px rgba(74, 108, 247, 0.3);
        }
        
        .score-label {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .score-value {
            font-size: 48px;
            font-weight: 700;
            line-height: 1;
        }
        
        .score-max {
            font-size: 16px;
            margin-top: 5px;
            opacity: 0.8;
        }
        
        /* Interpretation section */
        .interpretation {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin: 30px 0;
            border-left: 5px solid #4a6cf7;
        }
        
        .interpretation h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .interpretation p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .interpretation p:last-child {
            margin-bottom: 0;
        }
        
        /* Recommendations section */
        .recommendations {
            margin: 30px 0;
        }
        
        .recommendations h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .recommendation-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #4a6cf7;
        }
        
        .recommendation-card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .recommendation-card p {
            color: #555;
            line-height: 1.6;
        }
        
        /* Answer review section */
        .answer-review {
            margin: 40px 0;
        }
        
        .answer-review h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .question-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid;
        }
        
        .question-card.correct {
            border-color: #4CAF50;
        }
        
        .question-card.incorrect {
            border-color: #F44336;
        }
        
        .question-card h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .answer-item {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        
        .your-answer {
            background-color: #e3f2fd;
        }
        
        .correct-answer {
            background-color: #e8f5e9;
        }
        
        .answer-label {
            font-weight: 600;
            margin-right: 10px;
        }
        
        /* Button styles */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 40px 0 20px;
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
        
        /* Share section */
        .share-section {
            text-align: center;
            margin: 30px 0;
        }
        
        .share-section h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .share-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .share-button:hover {
            transform: translateY(-3px);
        }
        
        .facebook {
            background-color: #3b5998;
        }
        
        .twitter {
            background-color: #1da1f2;
        }
        
        .linkedin {
            background-color: #0077b5;
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
            font-size: 14px;
        }
        
        /* IQ scale visualization */
        .iq-scale {
            margin: 40px 0;
            position: relative;
            height: 60px;
        }
        
        .scale-bar {
            height: 10px;
            background: linear-gradient(to right, #FF5252, #FFEB3B, #4CAF50, #2196F3, #9C27B0);
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .scale-markers {
            display: flex;
            justify-content: space-between;
            position: relative;
        }
        
        .scale-marker {
            position: relative;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        
        .scale-marker::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 10px;
            background-color: #666;
        }
        
        .your-iq-marker {
            position: absolute;
            top: -25px;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background-color: #4a6cf7;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .your-iq-label {
            position: absolute;
            top: -55px;
            transform: translateX(-50%);
            background-color: #4a6cf7;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
        }
        
        .your-iq-label::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #4a6cf7;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .results-container {
                padding: 30px 20px;
            }
            
            .score-circle {
                width: 160px;
                height: 160px;
            }
            
            .score-value {
                font-size: 38px;
            }
            
            .btn-container {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
            
            .iq-scale {
                margin: 30px 0;
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
        <div class="results-container">
            <div class="results-header">
                <h1>Your IQ Test Results</h1>
                <p>Based on your answers, we've calculated your IQ score and prepared personalized insights.</p>
            </div>
            
            <div class="score-display">
                <div class="score-circle">
                    <div class="score-label">Your IQ Score</div>
                    <div class="score-value"><?php echo $iqScore; ?></div>
                    <div class="score-max">out of 145+</div>
                </div>
            </div>
            
            <div class="iq-scale">
                <div class="scale-bar"></div>
                <div class="scale-markers">
                    <div class="scale-marker">70</div>
                    <div class="scale-marker">85</div>
                    <div class="scale-marker">100</div>
                    <div class="scale-marker">115</div>
                    <div class="scale-marker">130</div>
                    <div class="scale-marker">145+</div>
                </div>
                
                <?php 
                // Calculate position percentage for the IQ marker (70 to 145 range)
                $position = (($iqScore - 70) / 75) * 100;
                $position = max(0, min(100, $position)); // Ensure it's between 0-100%
                ?>
                
                <div class="your-iq-marker" style="left: <?php echo $position; ?>%"></div>
                <div class="your-iq-label" style="left: <?php echo $position; ?>%">Your IQ: <?php echo $iqScore; ?></div>
            </div>
            
            <div class="interpretation">
                <h2>What Your Score Means</h2>
                <p><?php echo htmlspecialchars($resultInterpretation['result_text'] ?? 'Your IQ score indicates your cognitive abilities compared to the general population.'); ?></p>
                <p>You answered <?php echo $score; ?> out of <?php echo $_SESSION['total_questions']; ?> questions correctly.</p>
            </div>
            
            <div class="recommendations">
                <h2>Personalized Recommendations</h2>
                <div class="recommendation-card">
                    <h3>How to Improve Your Cognitive Skills</h3>
                    <p><?php echo htmlspecialchars($resultInterpretation['recommendation'] ?? 'Regular mental exercises and learning new skills can help improve your cognitive abilities.'); ?></p>
                </div>
            </div>
            
            <div class="answer-review">
                <h2>Your Answers Review</h2>
                
                <?php foreach ($userAnswers as $questionId => $answer): ?>
                    <div class="question-card <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
                        <h3>Question <?php echo $questionId; ?>: <?php echo htmlspecialchars($answer['question_text']); ?></h3>
                        <div class="answer-item your-answer">
                            <span class="answer-label">Your answer:</span>
                            <?php echo htmlspecialchars($answer['selected_option']); ?>
                        </div>
                        
                        <?php if (!$answer['is_correct']): ?>
                            <div class="answer-item correct-answer">
                                <span class="answer-label">Correct answer:</span>
                                <?php echo htmlspecialchars($correctAnswers[$questionId]['option_text']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="share-section">
                <h3>Share Your Results</h3>
                <div class="share-buttons">
                    <a href="#" class="share-button facebook">f</a>
                    <a href="#" class="share-button twitter">t</a>
                    <a href="#" class="share-button linkedin">in</a>
                </div>
            </div>
            
            <div class="btn-container">
                <a href="quiz.php?restart=true" class="btn">Take Test Again</a>
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p class="footer-text">&copy; <?php echo date('Y'); ?> IQTest. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Share functionality
        document.querySelectorAll('.share-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const shareText = `I just took an IQ test and scored ${<?php echo $iqScore; ?>}! Take the test yourself and see how you compare.`;
                const shareUrl = window.location.href;
                
                let shareLink = '';
                
                if (this.classList.contains('facebook')) {
                    shareLink = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}&quote=${encodeURIComponent(shareText)}`;
                } else if (this.classList.contains('twitter')) {
                    shareLink = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
                } else if (this.classList.contains('linkedin')) {
                    shareLink = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`;
                }
                
                window.open(shareLink, '_blank', 'width=600,height=400');
            });
        });
    </script>
</body>
</html>
