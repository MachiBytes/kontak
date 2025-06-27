<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Manager</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #141318;
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Desktop Sidebar */
        .sidebar {
            width: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #4338ca;
        }
        
        .nav-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .nav-btn {
            padding: 1.2rem 0.25rem;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }
        
        /* Mobile Header */
        .mobile-header {
            display: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            align-items: center;
            justify-content: space-between;
        }
        
        .mobile-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #4338ca;
        }
        
        .mobile-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .mobile-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
        }
        
        .search-icon-btn {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .search-icon-btn:hover {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .contacts-section {
            flex: 1;
            padding: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .search-container {
            position: relative;
            max-width: 400px;
        }
        
        .search-bar {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .search-bar:focus {
            outline: none;
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            width: 20px;
            height: 20px;
        }
        
        .contacts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .contact-list {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .list-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .contact-item:hover {
            background: rgba(79, 70, 229, 0.05);
            transform: translateX(4px);
        }
        
        .contact-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .contact-info h4 {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
        }
        
        .contact-info p {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        /* Profile Section */
        .profile-section {
            width: 350px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
        }
        
        .profile-card {
            text-align: center;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
            font-weight: 700;
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .profile-role {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        
        .profile-details {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: left;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .detail-item:hover {
            background: rgba(79, 70, 229, 0.05);
        }
        
        .detail-icon {
            width: 20px;
            height: 20px;
            color: #4f46e5;
        }
        
        .detail-text {
            color: #374151;
            font-weight: 500;
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                display: none;
            }
            
            .mobile-header {
                display: flex;
            }
            
            .main-content {
                flex-direction: column;
            }
            
            .contacts-section {
                padding: 1rem;
            }
            
            .section-header {
                display: none;
            }
            
            .contacts-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .profile-section {
                display: none;
            }
            
            .contact-list {
                padding: 1rem;
            }
            
            .contact-item {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Desktop Sidebar -->
        <nav class="sidebar">
            <div class="logo">
                <i data-lucide="users"></i>
            </div>
            <div class="nav-buttons">
                <button class="nav-btn">
                    <i data-lucide="plus"></i>
                </button>
                <button class="nav-btn">
                    <i data-lucide="settings"></i>
                </button>
            </div>
        </nav>
        
        <!-- Mobile Header -->
        <header class="mobile-header">
            <div class="mobile-logo">
                <i data-lucide="users"></i>
                ContactHub
            </div>
            <div class="mobile-controls">
                <span class="mobile-title">Contacts</span>
                <button class="search-icon-btn" onclick="toggleMobileSearch()">
                    <i data-lucide="search"></i>
                </button>
            </div>
        </header>
        
        <div class="main-content">
            <!-- Contacts Section -->
            <main class="contacts-section">
                <div class="section-header">
                    <h1 class="section-title">Contacts</h1>
                    <div class="search-container">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" class="search-bar" placeholder="Search contacts..." id="searchInput">
                    </div>
                </div>
                
                <div class="contacts-grid">
                    <div class="contact-list">
                        <h3 class="list-title">
                            <i data-lucide="star"></i>
                            Favorites
                        </h3>
                        <div class="contact-item">
                            <div class="contact-avatar">JS</div>
                            <div class="contact-info">
                                <h4>John Smith</h4>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-avatar">AD</div>
                            <div class="contact-info">
                                <h4>Alice Davis</h4>
                                <p>+1 (555) 987-6543</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-avatar">MJ</div>
                            <div class="contact-info">
                                <h4>Mike Johnson</h4>
                                <p>+1 (555) 456-7890</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-list">
                        <h3 class="list-title">
                            <i data-lucide="users"></i>
                            Recent
                        </h3>
                        <div class="contact-item">
                            <div class="contact-avatar">SW</div>
                            <div class="contact-info">
                                <h4>Sarah Wilson</h4>
                                <p>+1 (555) 234-5678</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-avatar">RB</div>
                            <div class="contact-info">
                                <h4>Robert Brown</h4>
                                <p>+1 (555) 345-6789</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-avatar">EL</div>
                            <div class="contact-info">
                                <h4>Emma Lee</h4>
                                <p>+1 (555) 567-8901</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <!-- Profile Section (Desktop Only) -->
            <aside class="profile-section">
                <div class="profile-card">
                    <div class="profile-avatar">CA</div>
                    <h2 class="profile-name">Claude Assistant</h2>
                    <p class="profile-role">AI Assistant</p>
                    
                    <div class="profile-details">
                        <div class="detail-item">
                            <i data-lucide="mail" class="detail-icon"></i>
                            <span class="detail-text">claude@anthropic.com</span>
                        </div>
                        <div class="detail-item">
                            <i data-lucide="phone" class="detail-icon"></i>
                            <span class="detail-text">+1 (555) 000-0000</span>
                        </div>
                        <div class="detail-item">
                            <i data-lucide="map-pin" class="detail-icon"></i>
                            <span class="detail-text">San Francisco, CA</span>
                        </div>
                        <div class="detail-item">
                            <i data-lucide="building" class="detail-icon"></i>
                            <span class="detail-text">Anthropic</span>
                        </div>
                        <div class="detail-item">
                            <i data-lucide="calendar" class="detail-icon"></i>
                            <span class="detail-text">Available 24/7</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const contacts = document.querySelectorAll('.contact-item');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                contacts.forEach(contact => {
                    const name = contact.querySelector('h4').textContent.toLowerCase();
                    const phone = contact.querySelector('p').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || phone.includes(searchTerm)) {
                        contact.style.display = 'flex';
                    } else {
                        contact.style.display = 'none';
                    }
                });
            });
        }
        
        // Mobile search toggle
        function toggleMobileSearch() {
            // This would open a mobile search overlay in a real app
            alert('Mobile search functionality would be implemented here');
        }
        
        // Contact item click handlers
        contacts.forEach(contact => {
            contact.addEventListener('click', function() {
                const name = this.querySelector('h4').textContent;
                console.log(`Selected contact: ${name}`);
                // In a real app, this would show contact details or perform an action
            });
        });
    </script>
</body>
</html>