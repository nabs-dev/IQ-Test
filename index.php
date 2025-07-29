<?php
// Start session to track user progress
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQ Test - Measure Your Intelligence</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header styles */
        header {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            position: relative;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #4a6cf7;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .logo span {
            color: #333;
        }
        
        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 30px;
        }
        
        nav a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 16px;
        }
        
        nav a:hover {
            color: #4a6cf7;
        }
        
        /* Hero section */
        .hero {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            margin: 40px 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 60px 30px;
        }
        
        .hero h1 {
            font-size: 42px;
            margin-bottom: 20px;
            color: #333;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 18px;
            line-height: 1.6;
            color: #666;
            max-width: 800px;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            background-color: #4a6cf7;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 18px;
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
        
        /* Features section */
        .features {
            padding: 60px 0;
        }
        
        .section-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 50px;
            color: #333;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background-color: #4a6cf7;
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }
        
        .feature-icon {
            font-size: 40px;
            color: #4a6cf7;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        /* How it works section */
        .how-it-works {
            padding: 60px 0;
            background-color: #f8f9fa;
            border-radius: 12px;
            margin: 40px 0;
        }
        
        .steps {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-top: 40px;
        }
        
        .step {
            flex: 1;
            min-width: 250px;
            max-width: 350px;
            text-align: center;
            position: relative;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background-color: #4a6cf7;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 20px;
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.3);
        }
        
        .step h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .step p {
            color: #666;
            line-height: 1.6;
        }
        
        /* Footer */
        footer {
            background-color: #fff;
            padding: 40px 0;
            margin-top: auto;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 40px;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: 700;
            color: #4a6cf7;
            margin-bottom: 15px;
        }
        
        .footer-logo span {
            color: #333;
        }
        
        .footer-text {
            max-width: 400px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .footer-links h4 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            text-decoration: none;
            color: #666;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: #4a6cf7;
        }
        
        .copyright {
            text-align: center;
            margin-top: 40px;
            color: #888;
            font-size: 14px;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .steps {
                flex-direction: column;
                align-items: center;
            }
            
            nav ul {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">IQ<span>Test</span></div>
            <nav>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#about">About IQ Tests</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <section class="hero">
            <h1>Discover Your Intelligence Quotient</h1>
            <p>Take our scientifically designed IQ test to measure your cognitive abilities, logical reasoning, and problem-solving skills. Get detailed insights about your intellectual strengths and areas for improvement.</p>
            <a href="quiz.php" class="btn">Start IQ Test Now</a>
        </section>
        
        <section id="features" class="features">
            <h2 class="section-title">Why Take Our IQ Test?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🧠</div>
                    <h3>Accurate Assessment</h3>
                    <p>Our test is designed by psychologists and cognitive scientists to provide an accurate measurement of your intellectual abilities.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Detailed Analysis</h3>
                    <p>Receive a comprehensive breakdown of your cognitive strengths and areas where you can improve.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⏱️</div>
                    <h3>Quick & Convenient</h3>
                    <p>Complete the test in just 15-20 minutes from any device and get your results instantly.</p>
                </div>
            </div>
        </section>
        
        <section id="how-it-works" class="how-it-works">
            <h2 class="section-title">How It Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Take the Test</h3>
                    <p>Answer a series of carefully designed questions that assess different aspects of intelligence.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Get Your Score</h3>
                    <p>Our algorithm calculates your IQ score based on your performance compared to others.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Review Insights</h3>
                    <p>Explore detailed feedback about your cognitive abilities and personalized recommendations.</p>
                </div>
            </div>
        </section>
        
        <section id="about" class="features">
            <h2 class="section-title">About IQ Tests</h2>
            <div class="feature-card">
                <p>Intelligence Quotient (IQ) tests are standardized assessments designed to measure human intelligence. They evaluate various cognitive abilities including logical reasoning, pattern recognition, mathematical ability, and verbal comprehension.</p>
                <p style="margin-top: 15px;">The average IQ score is set at 100, with approximately 68% of the population scoring between 85 and 115. Scores above 130 are considered exceptionally high, while scores below 70 may indicate cognitive challenges.</p>
                <p style="margin-top: 15px;">While IQ tests provide valuable insights into certain aspects of intelligence, they don't measure creativity, emotional intelligence, or practical skills. They're best used as one tool among many for understanding cognitive abilities.</p>
            </div>
        </section>
    </main>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div>
                    <div class="footer-logo">IQ<span>Test</span></div>
                    <p class="footer-text">Our mission is to provide accessible and accurate intelligence assessment tools to help people understand and develop their cognitive abilities.</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#about">About IQ Tests</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">IQ Research</a></li>
                        <li><a href="#">Cognitive Development</a></li>
                        <li><a href="#">Brain Training</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> IQTest. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
