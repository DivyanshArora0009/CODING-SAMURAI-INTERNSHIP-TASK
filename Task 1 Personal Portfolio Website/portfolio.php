<?php
// PHP Configuration and Functions
session_start();

// Process contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    // Sanitize and validate input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Validate input
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($message)) {
        $errors[] = "Message is required";
    }

    if (empty($errors)) {
        // In a real application, you would send an email here
        $success_message = "Thank you, $name! Your message has been sent successfully.";
        $_SESSION['success_message'] = $success_message;
        header('Location: ' . $_SERVER['PHP_SELF'] . '#contact');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        header('Location: ' . $_SERVER['PHP_SELF'] . '#contact');
        exit;
    }
}

// Sample Project Data
$projects = [
    [
        'title' => 'E-Commerce Platform',
        'description' => 'A full-stack e-commerce solution with payment integration and inventory management.',
        'technologies' => ['PHP', 'MySQL', 'JavaScript', 'Bootstrap'],
        'year' => '2024'
    ],
    [
        'title' => 'Task Management App',
        'description' => 'A productivity application for teams to collaborate on projects and tasks.',
        'technologies' => ['React', 'Node.js', 'MongoDB'],
        'year' => '2023'
    ],
    [
        'title' => 'Portfolio Website',
        'description' => 'A responsive portfolio website showcasing creative work and skills.',
        'technologies' => ['HTML5', 'CSS3', 'JavaScript'],
        'year' => '2022'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divyansh Arora | Portfolio</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        }

        .project-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .skill-bar {
            height: 6px;
            border-radius: 3px;
        }

        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .mobile-menu.active {
            max-height: 500px;
        }

        .social-icon {
            transition: transform 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-blue-600">Divyansh Arora</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-gray-700 hover:text-blue-600 transition">About</a>
                    <a href="#projects" class="text-gray-700 hover:text-blue-600 transition">Projects</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 transition">Contact</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu md:hidden bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#about" class="block px-3 py-2 text-gray-700 hover:text-blue-600">About</a>
                <a href="#projects" class="block px-3 py-2 text-gray-700 hover:text-blue-600">Projects</a>
                <a href="#contact" class="block px-3 py-2 text-gray-700 hover:text-blue-600">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-gradient text-white pt-24 pb-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Hi, I'm Divyansh Arora</h1>
                    <p class="text-xl mb-6">Full Stack Developer & Software Engineer</p>
                    <div class="flex space-x-4">
                        <a href="#contact"
                            class="bg-white text-blue-600 px-6 py-2 rounded-md font-medium hover:bg-gray-100 transition">Get
                            In Touch</a>
                        <a href="#projects"
                            class="border border-white text-white px-6 py-2 rounded-md font-medium hover:bg-white hover:text-blue-600 transition">View
                            Work</a>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">About Me</h2>
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0 md:pr-10">
                    <!-- Workspace Image -->
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80"
                        alt="Divyansh Arora's workspace" class="rounded-lg shadow-md w-full" />
                </div>
                <div class="md:w-1/2">
                    <p class="text-lg mb-6">I'm a passionate full-stack developer with 3+ years of experience building
                        web applications. I specialize in creating responsive, user-friendly interfaces with clean,
                        efficient code.</p>
                    <p class="text-lg mb-6">My journey in software development began during my computer science studies,
                        and I've since worked with startups and established companies to deliver high-quality digital
                        solutions.</p>

                    <h3 class="text-xl font-semibold mb-4">Technical Skills</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span>JavaScript/TypeScript</span>
                                <span>90%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full skill-bar">
                                <div class="bg-blue-600 skill-bar" style="width: 90%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span>PHP & Laravel</span>
                                <span>85%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full skill-bar">
                                <div class="bg-blue-600 skill-bar" style="width: 85%;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span>React & Node.js</span>
                                <span>88%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full skill-bar">
                                <div class="bg-blue-600 skill-bar" style="width: 88%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="py-20 bg-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Featured Projects</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($projects as $project): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden project-card">
                        <div class="h-48 overflow-hidden">
                            <!-- Project Screenshot -->
                            <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80"
                                alt="<?php echo htmlspecialchars($project['title']); ?> project"
                                class="w-full h-full object-cover" />
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($project['title']); ?></h3>
                            <span
                                class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mb-3"><?php echo htmlspecialchars($project['year']); ?></span>
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($project['description']); ?></p>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($project['technologies'] as $tech): ?>
                                    <span
                                        class="bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded"><?php echo htmlspecialchars($tech); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Get In Touch</h2>
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2 mb-8 md:mb-0 md:pr-10">
                    <h3 class="text-xl font-semibold mb-4">Contact Information</h3>
                    <p class="mb-6">Feel free to reach out for project inquiries or just to say hello. I'm always open
                        to discuss new opportunities.</p>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <span class="ml-3">divyansharrora16nov@gmail.com</span>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600">
                                <i class="fas fa-phone text-xl"></i>
                            </div>
                            <span class="ml-3">+91 88607 20565</span>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-blue-600">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <span class="ml-3">Ghaziabad, Uttar Pradesh, India</span>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold mb-4">Connect with me</h4>
                        <div class="flex space-x-4">
                            <a href="https://github.com/DivyanshArora0009" class="social-icon text-blue-600 hover:text-blue-800">
                                <i class="fab fa-github text-2xl"></i>
                            </a>
                        
                            <a href="https://www.linkedin.com/in/divyansharora2002?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" class="social-icon text-blue-700 hover:text-blue-900">
                                <i class="fab fa-linkedin-in text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline"><?php echo $_SESSION['success_message']; ?></span>
                            <?php unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p class="block sm:inline"><?php echo $error; ?></p>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['errors']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea id="message" name="message" rows="4" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <button type="submit" name="contact_submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-md font-medium hover:bg-blue-700 transition">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p>© <?php echo date('Y'); ?> Divyansh Arora. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a href="https://github.com/DivyanshArora0009" class="social-icon text-gray-400 hover:text-white">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                
                    <a href="https://www.linkedin.com/in/divyansharora2002?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" class="social-icon text-gray-400 hover:text-white">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('active');
        });

        // Close mobile menu when clicking on links
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.remove('active');
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Form validation
        const contactForm = document.querySelector('form');
        if (contactForm) {
            contactForm.addEventListener('submit', function (e) {
                let valid = true;
                const name = document.getElementById('name');
                const email = document.getElementById('email');
                const message = document.getElementById('message');

                if (!name.value.trim()) {
                    valid = false;
                    name.classList.add('border-red-500');
                } else {
                    name.classList.remove('border-red-500');
                }

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value)) {
                    valid = false;
                    email.classList.add('border-red-500');
                } else {
                    email.classList.remove('border-red-500');
                }

                if (!message.value.trim()) {
                    valid = false;
                    message.classList.add('border-red-500');
                } else {
                    message.classList.remove('border-red-500');
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        }
    </script>
</body>

</html>