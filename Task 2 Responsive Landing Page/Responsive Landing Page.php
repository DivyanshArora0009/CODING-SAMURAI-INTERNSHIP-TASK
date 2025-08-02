<?php
/**
 * MarketPro - Digital Marketing Landing Page
 * Created for Divyansh Arora
 * 
 * This PHP file contains a responsive landing page with:
 * - HTML5 structure
 * - Embedded CSS with media queries
 * - JavaScript functionality
 * - PHP form handling
 * - Properly commented sections
 */

// Basic configuration and form handling
$formSubmitted = false;
$formErrors = [];
$name = $email = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form validation
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name)) {
        $formErrors['name'] = 'Name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formErrors['email'] = 'Valid email is required';
    }
    if (empty($message)) {
        $formErrors['message'] = 'Message is required';
    }

    if (empty($formErrors)) {
        $formSubmitted = true;
        // Here you would typically send an email or save to database
        // For demo purposes, we'll just set the submitted flag
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPro | Digital Marketing Solutions for Divyansh Arora</title>
    <!-- CSS styles embedded -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles */
        :root {
            --primary-color: #3a86ff;
            --secondary-color: #8338ec;
            --accent-color: #ff006e;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --text-color: #333;
            --text-light: #666;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-color);
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
        }

        /* Utility classes */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .section {
            padding: 80px 0;
        }

        .section-title {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            text-align: center;
            position: relative;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            margin: 15px auto;
            border-radius: 2px;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-light);
            text-align: center;
            max-width: 700px;
            margin: 0 auto 50px;
        }

        /* Header styles */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .logo span {
            color: var(--primary-color);
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            color: var(--dark-color);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Hero section */
        .hero {
            padding: 180px 0 100px;
            background: linear-gradient(135deg, rgba(58, 134, 255, 0.1), rgba(131, 56, 236, 0.1));
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--text-light);
            margin-bottom: 2.5rem;
        }

        .hero-image {
            margin-top: 50px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .hero-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Features section */
        .features {
            background-color: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .feature-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            color: white;
            font-size: 2rem;
        }

        .feature-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--dark-color);
        }

        .feature-desc {
            color: var(--text-light);
        }

        /* Testimonials section */
        .testimonials {
            background-color: var(--light-color);
        }

        .testimonials-slider {
            margin-top: 50px;
            position: relative;
        }

        .testimonial {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 0 15px;
        }

        .testimonial-content {
            font-style: italic;
            margin-bottom: 20px;
            color: var(--text-color);
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-author-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }

        .testimonial-author-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-info h4 {
            font-size: 1.1rem;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .author-info p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Pricing section */
        .pricing {
            background-color: white;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .pricing-card {
            background-color: white;
            border-radius: 10px;
            padding: 40px 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .pricing-card.popular {
            border: 2px solid var(--primary-color);
            position: relative;
        }

        .popular-badge {
            position: absolute;
            top: -15px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .pricing-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--dark-color);
        }

        .pricing-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .pricing-price span {
            font-size: 1rem;
            color: var(--text-light);
        }

        .pricing-features {
            list-style: none;
            margin-bottom: 30px;
        }

        .pricing-features li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            color: var(--text-light);
        }

        .pricing-features li:last-child {
            border-bottom: none;
        }

        /* Contact section */
        .contact {
            background: linear-gradient(135deg, rgba(58, 134, 255, 0.1), rgba(131, 56, 236, 0.1));
        }

        .contact-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
            margin-top: 50px;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            flex-shrink: 0;
        }

        .contact-details h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark-color);
        }

        .contact-details p, .contact-details a {
            color: var(--text-light);
        }

        .contact-form {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .success-message {
            color: #28a745;
            text-align: center;
            font-weight: 500;
            margin-bottom: 20px;
        }

        /* Footer */
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 50px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-col h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: white;
        }

        .footer-col p, .footer-col a {
            color: #aaa;
            margin-bottom: 10px;
            display: block;
            transition: color 0.3s;
        }

        .footer-col a:hover {
            color: white;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }

        .social-link:hover {
            background-color: var(--primary-color);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #aaa;
            font-size: 0.9rem;
        }

        .copyright a {
            color: var(--primary-color);
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .section-title {
                font-size: 2.2rem;
            }

            .hero-title {
                font-size: 3rem;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                right: 0;
                background-color: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            }

            .nav-links.show {
                display: flex;
            }

            .nav-links li {
                margin: 10px 0;
            }

            .mobile-menu-btn {
                display: block;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section {
                padding: 60px 0;
            }
        }

        @media (max-width: 576px) {
            .hero {
                padding: 150px 0 80px;
            }

            .hero-title {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .pricing-grid, .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header with navigation -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">Market<span>Pro</span></a>
                <ul class="nav-links" id="navLinks">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <button class="mobile-menu-btn" id="mobileMenuBtn">☰</button>
            </nav>
        </div>
    </header>

    <!-- Hero section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Grow Your Business with Our Digital Marketing Solutions</h1>
                <p class="hero-subtitle">We help businesses like yours increase online visibility, generate leads, and boost sales through our comprehensive digital marketing strategies.</p>
                <a href="#contact" class="btn btn-primary">Get Started</a>
            </div>
            <div class="hero-image">
                <svg viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
                    <rect width="100%" height="100%" fill="#f0f7ff" />
                    <circle cx="200" cy="200" r="80" fill="#3a86ff" opacity="0.2" />
                    <circle cx="600" cy="150" r="60" fill="#8338ec" opacity="0.2" />
                    <circle cx="400" cy="250" r="40" fill="#ff006e" opacity="0.2" />
                    <rect x="100" y="100" width="600" height="200" rx="10" fill="white" stroke="#3a86ff" stroke-width="2" />
                    <line x1="150" y1="150" x2="300" y2="150" stroke="#3a86ff" stroke-width="3" />
                    <line x1="150" y1="180" x2="250" y2="180" stroke="#8338ec" stroke-width="3" />
                    <line x1="150" y1="210" x2="200" y2="210" stroke="#ff006e" stroke-width="3" />
                    <circle cx="500" cy="150" r="30" fill="#3a86ff" opacity="0.3" />
                    <circle cx="500" cy="150" r="20" fill="#3a86ff" opacity="0.5" />
                    <circle cx="500" cy="150" r="10" fill="#3a86ff" />
                    <rect x="400" y="180" width="150" height="20" rx="10" fill="#8338ec" opacity="0.5" />
                    <rect x="400" y="210" width="200" height="20" rx="10" fill="#ff006e" opacity="0.5" />
                    <text x="150" y="130" font-family="Arial" font-size="14" fill="#1a1a2e">Marketing Analytics Dashboard</text>
                </svg>
            </div>
        </div>
    </section>

    <!-- Features section -->
    <section class="section features" id="features">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">We offer a complete range of digital marketing services to help your business thrive online.</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        📈
                    </div>
                    <h3 class="feature-title">SEO Optimization</h3>
                    <p class="feature-desc">Improve your search engine rankings with our proven SEO strategies that drive organic traffic to your website.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        💬
                    </div>
                    <h3 class="feature-title">Social Media</h3>
                    <p class="feature-desc">Build brand awareness and engage with your audience through strategic social media campaigns.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        📧
                    </div>
                    <h3 class="feature-title">Email Marketing</h3>
                    <p class="feature-desc">Convert leads into customers with our targeted email marketing campaigns that deliver results.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        📢
                    </div>
                    <h3 class="feature-title">PPC Advertising</h3>
                    <p class="feature-desc">Get immediate traffic and leads with our high-converting pay-per-click advertising campaigns.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        ✍️
                    </div>
                    <h3 class="feature-title">Content Creation</h3>
                    <p class="feature-desc">Attract and engage your audience with high-quality content tailored to your brand and goals.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        📱
                    </div>
                    <h3 class="feature-title">Web Development</h3>
                    <p class="feature-desc">Get a fast, responsive website that converts visitors into customers with our development expertise.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials section -->
    <section class="section testimonials" id="testimonials">
        <div class="container">
            <h2 class="section-title">What Our Clients Say</h2>
            <p class="section-subtitle">Don't just take our word for it - hear from our satisfied customers.</p>
            
            <div class="testimonials-slider">
                <div class="testimonial">
                    <p class="testimonial-content">Working with MarketPro was a game-changer for our business. Our website traffic increased by 300% within just 3 months of implementing their SEO strategies.</p>
                    <div class="testimonial-author">
                        <div class="testimonial-author-img">
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="50" fill="#3a86ff" />
                                <text x="50" y="55" font-family="Arial" font-size="40" fill="white" text-anchor="middle" dominant-baseline="middle">SJ</text>
                            </svg>
                        </div>
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <p>CEO, TechSolutions</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial" style="display:none;">
                    <p class="testimonial-content">The social media campaign created by MarketPro tripled our engagement rate and brought in 50% more leads than we were getting previously. Highly recommended!</p>
                    <div class="testimonial-author">
                        <div class="testimonial-author-img">
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="50" fill="#8338ec" />
                                <text x="50" y="55" font-family="Arial" font-size="40" fill="white" text-anchor="middle" dominant-baseline="middle">MR</text>
                            </svg>
                        </div>
                        <div class="author-info">
                            <h4>Michael Rodriguez</h4>
                            <p>Marketing Director, BrandCo</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial" style="display:none;">
                    <p class="testimonial-content">Their email marketing strategy has consistently delivered a 25% open rate and 8% click-through rate, which is well above industry averages. Great ROI!</p>
                    <div class="testimonial-author">
                        <div class="testimonial-author-img">
                            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="50" fill="#ff006e" />
                                <text x="50" y="55" font-family="Arial" font-size="40" fill="white" text-anchor="middle" dominant-baseline="middle">AP</text>
                            </svg>
                        </div>
                        <div class="author-info">
                            <h4>Aisha Patel</h4>
                            <p>E-commerce Manager, ShopEasy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing section -->
    <section class="section pricing" id="pricing">
        <div class="container">
            <h2 class="section-title">Pricing Plans</h2>
            <p class="section-subtitle">Choose the perfect plan for your business needs and budget.</p>
            
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3 class="pricing-title">Starter</h3>
                    <div class="pricing-price">$299<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>Basic SEO Optimization</li>
                        <li>Social Media Setup</li>
                        <li>4 Blog Posts Monthly</li>
                        <li>Monthly Analytics Report</li>
                        <li>Email Support</li>
                    </ul>
                    <a href="#contact" class="btn btn-primary">Get Started</a>
                </div>
                
                <div class="pricing-card popular">
                    <span class="popular-badge">Most Popular</span>
                    <h3 class="pricing-title">Professional</h3>
                    <div class="pricing-price">$799<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>Advanced SEO Strategies</li>
                        <li>Social Media Management</li>
                        <li>8 Blog Posts Monthly</li>
                        <li>Email Marketing Campaigns</li>
                        <li>PPC Campaign Management</li>
                        <li>Weekly Analytics Report</li>
                        <li>Priority Support</li>
                    </ul>
                    <a href="#contact" class="btn btn-primary">Get Started</a>
                </div>
                
                <div class="pricing-card">
                    <h3 class="pricing-title">Enterprise</h3>
                    <div class="pricing-price">$1,499<span>/month</span></div>
                    <ul class="pricing-features">
                        <li>Comprehensive SEO & PPC</li>
                        <li>Complete Social Media Strategy</li>
                        <li>Content Marketing Plan</li>
                        <li>Unlimited Blog Posts</li>
                        <li>Advanced Analytics Dashboard</li>
                        <li>Dedicated Account Manager</li>
                        <li>24/7 Support</li>
                    </ul>
                    <a href="#contact" class="btn btn-primary">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact section -->
    <section class="section contact" id="contact">
        <div class="container">
            <h2 class="section-title">Get In Touch</h2>
            <p class="section-subtitle">Have questions or ready to get started? Contact us today for a free consultation.</p>
            
            <div class="contact-container">
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            📍
                        </div>
                        <div class="contact-details">
                            <h3>Our Office</h3>
                            <p>Mahendra Enclave<br>Ghaziabad, India 201002</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            📞
                        </div>
                        <div class="contact-details">
                            <h3>Phone</h3>
                            <a href="tel:+918860720565">+91 88607 20565</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            ✉️
                        </div>
                        <div class="contact-details">
                            <h3>Email</h3>
                            <a href="mailto:divyansharrora16nov@gmail.com">info@marketpro.com</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            ⏰
                        </div>
                        <div class="contact-details">
                            <h3>Business Hours</h3>
                            <p>Monday-Friday: 9AM - 6PM<br>Saturday: 10AM - 2PM</p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <?php if ($formSubmitted): ?>
                        <div class="success-message">
                            Thank you for your message! We'll get back to you within 24 hours.
                        </div>
                    <?php else: ?>
                        <form method="POST" action="#contact" id="contactForm">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                                <?php if (isset($formErrors['name'])): ?>
                                    <div class="error-message"><?= $formErrors['name'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                                <?php if (isset($formErrors['email'])): ?>
                                    <div class="error-message"><?= $formErrors['email'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Your Message</label>
                                <textarea id="message" name="message" class="form-control" required><?= htmlspecialchars($message) ?></textarea>
                                <?php if (isset($formErrors['message'])): ?>
                                    <div class="error-message"><?= $formErrors['message'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                Send Message
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3>MarketPro</h3>
                    <p>Helping businesses grow through innovative digital marketing solutions since 2023.</p>
                    <div class="social-links">
                        <a href="https://github.com/DivyanshArora0009" class="social-icon text-blue-600 hover:text-blue-800">
                                <i class="fab fa-github text-2xl"></i>
                            </a>
                            <a href="https://www.linkedin.com/in/divyansharora2002?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" class="social-icon text-blue-700 hover:text-blue-900">
                                <i class="fab fa-linkedin-in text-2xl"></i>
                            </a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <a href="#features">Services</a>
                    <a href="#testimonials">Testimonials</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#contact">Contact</a>
                </div>
                
                <div class="footer-col">
                    <h3>Services</h3>
                    <a href="#">SEO Optimization</a>
                    <a href="#">Social Media</a>
                    <a href="#">PPC Advertising</a>
                    <a href="#">Content Marketing</a>
                </div>
                
                <div class="footer-col">
                    <h3>Legal</h3>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
            
            <div class="copyright">
                <p>© <?= date('Y') ?> MarketPro. All rights reserved. Created by <a href="#">Divyansh Arora</a></p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('show');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const target = document.querySelector(targetId);
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const navLinks = document.getElementById('navLinks');
                    if (navLinks.classList.contains('show')) {
                        navLinks.classList.remove('show');
                    }
                }
            });
        });

        // Form submission handler
        document.getElementById('contactForm')?.addEventListener('submit', function(e) {
            // Reset previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            
            let isValid = true;
            
            // Name validation
            const name = document.getElementById('name').value.trim();
            if (!name) {
                document.querySelector('#name + .error-message').textContent = 'Name is required';
                isValid = false;
            }
            
            // Email validation
            const email = document.getElementById('email').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                document.querySelector('#email + .error-message').textContent = 'Email is required';
                isValid = false;
            } else if (!emailPattern.test(email)) {
                document.querySelector('#email + .error-message').textContent = 'Please enter a valid email address';
                isValid = false;
            }
            
            // Message validation
            const message = document.getElementById('message').value.trim();
            if (!message) {
                document.querySelector('#message + .error-message').textContent = 'Message is required';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Simple testimonial slider
        let currentTestimonial = 0;
        const testimonials = document.querySelectorAll('.testimonials-slider .testimonial');
        
        function showTestimonial(index) {
            testimonials.forEach(testimonial => {
                testimonial.style.display = 'none';
            });
            
            testimonials[index].style.display = 'block';
        }
        
        // Initialize the slider
        if (testimonials.length > 0) {
            showTestimonial(0);
            
            // Auto-rotate testimonials every 5 seconds
            setInterval(() => {
                currentTestimonial = (currentTestimonial + 1) % testimonials.length;
                showTestimonial(currentTestimonial);
            }, 5000);
        }

        // Responsive adjustments
        function handleResize() {
            // Show nav links by default if screen is large
            const navLinks = document.getElementById('navLinks');
            if (window.innerWidth > 768) {
                navLinks.style.display = 'flex';
            } else {
                navLinks.style.display = 'none';
                navLinks.classList.remove('show');
            }
        }

        // Run on load and resize
        window.addEventListener('load', handleResize);
        window.addEventListener('resize', handleResize);
    </script>
</body>
</html>