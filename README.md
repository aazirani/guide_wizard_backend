# Guide Wizard Backend

A sophisticated backend system that powers the Guide Wizard mobile application. Built on UserFrosting PHP framework, this system provides intelligent content personalization, multilingual support, and comprehensive admin management for creating adaptive step-by-step guides.

## 🚀 Features

### Core Backend Functionality
- **Intelligent Logic Engine**: Advanced expression evaluation system for dynamic content personalization
- **Content Management System**: Comprehensive admin interface for managing steps, questions, tasks, and answers
- **Dynamic Form Generation**: Utilizes FormGenerator for flexible form creation and management
- **Dynamic Multilingual System**: Flexible translation management with technical name mapping
- **RESTful API Architecture**: Well-designed endpoints for seamless mobile app communication
- **Role-Based Access Control**: Secure authentication and authorization system
- **Cache Management**: Timestamp-based content validation and optimization

### API Capabilities
- **Personalized Content Delivery**: Returns customized content based on user answer selections
- **Translation Management**: Dynamic language support with extensible localization
- **Content Versioning**: Update tracking and cache invalidation system
- **Admin CRUD Operations**: Full content lifecycle management
- **Security Middleware**: Authentication, CSRF protection, and audit trails

## 🏗️ Architecture

This project is built using the **UserFrosting 4.6** framework with a modular sprinkle architecture.

> **⚠️ Version Compatibility**: Guide Wizard is specifically designed for **UserFrosting 4.6** and is **not compatible** with newer versions (5.0+). Please ensure you use UserFrosting 4.6 from the [4.6 branch](https://github.com/userfrosting/UserFrosting/tree/4.6).

### Project Structure
```
guide_wizard_backend/
├── app/
│   ├── sprinkles/
│   │   ├── core/              # UserFrosting core
│   │   ├── account/           # User management
│   │   ├── admin/             # Admin interface
│   │   └── guide_wizard/      # Guide Wizard custom sprinkle
│   │       ├── config/        # Configuration files
│   │       ├── routes/        # API route definitions
│   │       ├── src/
│   │       │   ├── Controller/    # API controllers
│   │       │   ├── Database/      # Models and migrations
│   │       │   └── ServicesProvider/ # Dependency injection
│   │       ├── templates/     # Twig templates
│   │       └── locale/        # Translation files
│   ├── logs/                  # Application logs
│   ├── cache/                 # Cached files
│   └── sessions/              # Session data
├── public/                    # Web root
├── build/                     # Build assets
└── vendor/                    # Composer dependencies
```

### Key Components

#### Data Models
- **Step**: Main organizational units containing questions and tasks
- **Question**: Interactive questionnaire components with multiple types
- **Answer**: User response options with image support and ordering
- **Task**: Actionable items with descriptions and media
- **SubTask**: Granular task components with conditional logic
- **Logic**: Expression-based rules for content personalization
- **Translation**: Multilingual content management system

#### Controllers
- **AppController**: Public API endpoints for mobile app
- **StepController**: Step management and CRUD operations
- **QuestionController**: Question lifecycle management
- **TaskController**: Task and subtask administration
- **TranslationController**: Multilingual content management

## 📋 Prerequisites

### System Requirements
- **PHP 7.3+** (PHP 8.0+ recommended)
- **MySQL 5.7+**, **MariaDB**, **PostgreSQL**, or **SQLite**
- **PDO and GD PHP Extensions** (required)
- **Composer** (latest version)
- **Node.js 10.12.0+** and **npm 6.0+** (for asset compilation)
- **Web server** (Apache with `mod_rewrite`, Nginx, or IIS)
- **Git** (for cloning repositories)

### Required Dependencies
- **UserFrosting 4.6**: Base framework (must use 4.6 branch)
- **FormGenerator 4.0**: Form generation library (must use 4.0 branch)

> **⚠️ Version Compatibility**:
> - UserFrosting 5.0+ is **not supported**
> - FormGenerator 5.x is **not compatible** with UserFrosting 4.6

## ⚡ Installation Guide

### 🐳 Easy Install with Docker (Recommended)

The easiest way to get started with Guide Wizard Backend is using our Docker setup. This handles all dependencies, UserFrosting installation, database configuration, and initial seeding automatically.

#### Prerequisites
- [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/) installed
- Git

#### Quick Start

1. **Clone this repository**
   ```bash
   git clone https://github.com/aazirani/guide_wizard_backend.git
   cd guide_wizard_backend/docker-setup
   ```

2. **Configure environment variables (optional)**

   Edit `.env` to customize database credentials and domain:
   ```bash
   DB_NAME=guidewizard_database
   DB_USER=guidewizard_user
   DB_PASSWORD=your_secure_password
   ```

3. **Start the containers**
   ```bash
   docker-compose up -d --build
   ```

4. **Run the installation script**
   ```bash
   docker exec guidewizard-php install-guidewizard.sh
   ```

   This will:
   - Clone UserFrosting 4.6
   - Install the Guide Wizard sprinkle
   - Install all Composer dependencies (including FormGenerator 4.0)
   - Configure the database connection
   - Run migrations and create database tables
   - Seed the database with initial data
   - Create an admin user (you'll be prompted for credentials)

5. **Access your application**
   - **Frontend**: http://localhost:8080
   - **Admin Panel**: http://localhost:8080/admin
   - **API Base**: http://localhost:8080/api/

#### Managing Your Installation

**View logs:**
```bash
docker-compose logs -f
```

**Access the PHP container:**
```bash
docker exec -it guidewizard-php bash
```

**Stop the containers:**
```bash
docker-compose down
```

**Restart the containers:**
```bash
docker-compose restart
```

**Run UserFrosting commands:**
```bash
docker exec guidewizard-php bash -c "cd /var/www/html && php bakery [command]"
```

#### Production Deployment

For production deployment, you can:
1. Update the `.env` file with your production domain
2. Remove the port mapping and use the external `websites-network` in `docker-compose.yml`
3. Set up SSL using Let's Encrypt (instructions in `docker-setup/README.md`)

---

### 📦 Manual Installation

Guide Wizard is a **UserFrosting 4.6 sprinkle** and requires a complete UserFrosting 4.6 installation first.

### Step 1: Install UserFrosting 4.6

> **Important**: You must use UserFrosting 4.6 specifically. Newer versions (5.0+) are not compatible.

```bash
# Clone UserFrosting 4.6
git clone -b 4.6 https://github.com/userfrosting/UserFrosting.git guide_wizard_backend
cd guide_wizard_backend

# Install PHP dependencies
composer install

# Set directory permissions (Linux/macOS)
chmod -R 775 app/cache app/logs app/sessions app/storage
# For web server permissions:
chown -R www-data:www-data app/cache app/logs app/sessions app/storage
```

### Step 2: Database Setup
Create a database and database user with read/write permissions:

```sql
CREATE DATABASE guide_wizard;
CREATE USER 'guide_wizard_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON guide_wizard.* TO 'guide_wizard_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 3: Run UserFrosting Setup
```bash
# Run the interactive setup wizard
php bakery bake
```

Follow the setup wizard to:
- Configure database credentials
- Set up SMTP (optional)
- Create master admin user account

### Step 4: Install Guide Wizard Sprinkle

```bash
# Clone Guide Wizard sprinkle into the sprinkles directory
git clone https://github.com/aazirani/guide_wizard_backend.git app/sprinkles/guide_wizard

# Alternative: Copy your existing Guide Wizard sprinkle
# cp -r /path/to/your/guide_wizard_sprinkle app/sprinkles/guide_wizard
```

### Step 5: Configure Sprinkles and Dependencies
Edit `app/sprinkles.json` to include the `guide_wizard` sprinkle and FormGenerator dependency:

```json
{
    "base": [
        "core",
        "account",
        "admin",
		"FormGenerator",
        "guide_wizard"
    ],
    "require": {
        "lcharette/uf_formgenerator": "^4.0.0"
    }
}
```

> **Note**: FormGenerator will be automatically downloaded and installed by Composer when you run the update commands in the next step.

### Step 6: Update Dependencies and Database

```bash
# Update composer autoloader and install FormGenerator automatically
composer update

# Alternative: Run bakery bake to update dependencies and run migrations
php bakery bake

# Install/update Node.js dependencies
npm install

# If you didn't use 'php bakery bake', run migrations manually
php bakery migrate
```

> **Note**: `composer update` will automatically download and install FormGenerator 4.0.x based on the requirement in sprinkles.json. The `php bakery bake` command will also handle dependency updates and run migrations.

### Step 7: Build Assets
```bash
# Compile frontend assets
npm run dev

# For production
npm run production
```

### Step 8: Set Web Server Document Root
Configure your web server to point to the `public/` directory:

**Apache Virtual Host Example:**
```apache
<VirtualHost *:80>
    ServerName guidewizard-backend.local
    DocumentRoot /path/to/guide_wizard_backend/public

    <Directory /path/to/guide_wizard_backend/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx Configuration Example:**
```nginx
server {
    listen 80;
    server_name guidewizard-backend.local;
    root /path/to/guide_wizard_backend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Step 9: Access Your Installation
- **Frontend**: `http://guidewizard-backend.local/`
- **Admin Panel**: `http://guidewizard-backend.local/admin`
- **API Base**: `http://guidewizard-backend.local/api/`

## 🔧 Development Setup

For development, you can use PHP's built-in server:

```bash
# Start development server
php -S localhost:8080 -t public/

# Access at: http://localhost:8080
```

## 🔧 Configuration

### Database Models
The system uses Eloquent ORM with the following key relationships:

```php
// Steps contain Questions and Tasks
Step -> hasMany(Question::class)
Step -> hasMany(Task::class)

// Questions have multiple Answers
Question -> hasMany(Answer::class)

// Tasks can have SubTasks with Logic rules
Task -> hasMany(SubTask::class)
SubTask -> belongsTo(Logic::class)

// Multilingual support
Translation -> belongsTo(Text::class)
Translation -> belongsTo(Language::class)
```

### API Endpoints

#### Public API (No Authentication Required)
```
GET  /api/app/translations              # Fetch all active translations
GET  /api/app/content/answerIds/{ids}   # Get personalized content
GET  /api/app/lastUpdates               # Check content timestamps
```

#### Admin API (Authentication Required)
```
# Steps Management
GET    /api/steps                       # List all steps
POST   /api/steps                       # Create new step
GET    /api/steps/{id}/edit             # Get step for editing
PUT    /api/steps/{id}                  # Update step
DELETE /api/steps/{id}                  # Delete step

# Questions Management
GET    /api/questions                   # List all questions
POST   /api/questions                   # Create new question
PUT    /api/questions/{id}              # Update question
DELETE /api/questions/{id}              # Delete question

# Similar patterns for Tasks, Answers, Logic, Translations
```

### Intelligent Content Personalization
The core feature of the backend is the logic evaluation system:

```php
// Example logic expression evaluation
$logic = Logic::where('name', 'show_housing_tasks')->first();
$userAnswers = [1, 3, 7]; // Selected answer IDs

if ($this->evaluateLogic($logic->expression, $userAnswers)) {
    // Include housing-related tasks in response
    $content['tasks'] = $this->getHousingTasks();
}
```

Logic expressions support:
- **Answer ID matching**: `1|2|3` (if user selected any of these answers)
- **AND operations**: `1&2` (user must have selected both)
- **OR operations**: `1|2` (user selected either)
- **Complex expressions**: `(1&2)|(3&4)` (nested conditions)

## 🔐 Security Features

### Authentication & Authorization
- **Role-based access control** with UserFrosting's built-in system
- **CSRF protection** on all state-changing operations
- **Request validation** and sanitization
- **Audit logging** for all admin operations

### API Security
- **Authentication middleware** for admin endpoints
- **Rate limiting** to prevent abuse
- **Input validation** and sanitization
- **SQL injection prevention** through Eloquent ORM

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php bakery test

# Run specific test suite
php bakery test --testsuite=Unit

# Run tests with coverage
php bakery test --coverage-html coverage/
```

### Test Structure
```
app/sprinkles/guide_wizard/tests/
├── Integration/           # API integration tests
├── Unit/                 # Unit tests for models and services
└── TestCase.php          # Base test class
```

## 📦 Deployment

### Production Setup
```bash
# Set production environment
APP_DEBUG=false

# Optimize autoloader
composer install --no-dev --optimize-autoloader

# Build production assets
npm run production

# Clear and cache configuration
php bakery clear:cache
php bakery cache:config
```

### Docker Deployment
```bash
# Build Docker image
docker build -t guide-wizard-backend .

# Run with Docker Compose
docker-compose up -d
```

### Web Server Configuration

#### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName guidewizard-api.com
    DocumentRoot /path/to/guide_wizard_backend/public

    <Directory /path/to/guide_wizard_backend/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name guidewizard-api.com;
    root /path/to/guide_wizard_backend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🐛 Troubleshooting

### UserFrosting 4.6 Specific Issues

**Wrong UserFrosting Version**:
If you accidentally cloned the wrong version:
```bash
# Check current version
git branch -a

# Switch to 4.6 branch if needed
git checkout 4.6
git pull origin 4.6

# Reinstall dependencies
composer install
```

**Sprinkle Not Loading**:
- Verify `app/sprinkles.json` includes `guide_wizard` in the `base` array
- Verify `app/sprinkles.json` includes `"lcharette/uf_formgenerator": "^4.0.0"` in the `require` object
- Ensure the sprinkle directory exists: `app/sprinkles/guide_wizard/`
- Check that `composer.json` exists in the sprinkle directory
- Run `composer update` after modifying sprinkles.json

**FormGenerator Issues**:
- Ensure sprinkles.json has the correct requirement: `"lcharette/uf_formgenerator": "^4.0.0"`
- FormGenerator 5.x is not compatible with UserFrosting 4.6
- Check that FormGenerator was installed: `composer show lcharette/uf_formgenerator`
- If FormGenerator is missing, run `composer update` or `php bakery bake`

**Bakery Command Issues**:
```bash
# If php bakery bake fails
php bakery debug

# Clear cache if commands aren't working
php bakery clear-cache

# Check UserFrosting requirements
php bakery debug
```

### Common Issues

**Permission Issues**:
```bash
# Fix file permissions (Linux/macOS)
chmod -R 775 app/logs app/cache app/sessions app/storage
chown -R www-data:www-data app/logs app/cache app/sessions app/storage

# Windows (run as administrator)
icacls app\logs /grant "IIS_IUSRS:(OI)(CI)F"
icacls app\cache /grant "IIS_IUSRS:(OI)(CI)F"
icacls app\sessions /grant "IIS_IUSRS:(OI)(CI)F"
icacls app\storage /grant "IIS_IUSRS:(OI)(CI)F"
```

**Database Connection Issues**:
- Run `php bakery debug` to check database configuration
- Verify database credentials during `php bakery bake` setup
- Ensure database server is running and accessible
- Check firewall settings for database port

**Missing PHP Extensions**:
```bash
# Check required extensions
php -m | grep -E "(pdo|gd|mbstring)"

# Install missing extensions (Ubuntu/Debian)
sudo apt-get install php-pdo php-gd php-mbstring

# Install missing extensions (CentOS/RHEL)
sudo yum install php-pdo php-gd php-mbstring
```

**Composer Issues**:
```bash
# Update Composer
composer self-update

# Clear Composer cache
composer clear-cache

# Reinstall dependencies
rm -rf vendor/ composer.lock
composer install

# If autoload issues with sprinkle
composer dump-autoload
```

**Asset Compilation Issues**:
```bash
# Clear node modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Rebuild assets
npm run dev

# Check for Node.js version issues
node --version  # Should be 10.12.0+
npm --version   # Should be 6.0+
```

### Performance Optimization
- **Enable OPCache** for PHP bytecode caching
- **Use Redis/Memcached** for session and cache storage
- **Optimize database** queries and add proper indexes
- **Enable gzip compression** in web server configuration

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Follow PSR-12 coding standards
4. Write tests for new functionality
5. Commit your changes (`git commit -m 'Add some amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Development Guidelines
- Follow UserFrosting development patterns
- Use dependency injection where appropriate
- Write comprehensive tests
- Document new API endpoints
- Follow semantic versioning for releases

## 📚 Documentation

### UserFrosting 4.6 Resources
- **[UserFrosting 4.6 Quick Start](https://github.com/userfrosting/learn/blob/4.6/pages/01.quick-start/docs.md)** - Installation and setup guide
- **[UserFrosting 4.6 Native Installation](https://github.com/userfrosting/learn/blob/4.6/pages/03.installation/01.environment/03.native/docs.md)** - Detailed installation instructions
- **[Creating Sprinkles](https://github.com/userfrosting/learn/blob/4.6/pages/05.sprinkles/03.first-site/docs.md)** - Guide to creating custom sprinkles
- **[Sprinkles Documentation](https://github.com/userfrosting/learn/tree/4.6/pages/05.sprinkles)** - Complete sprinkles documentation
- **[UserFrosting 4.6 Main Repository](https://github.com/userfrosting/UserFrosting/tree/4.6)** - Source code and issues

> **Note**: Make sure to use the **4.6 branch** documentation, as newer versions are not compatible.

### General UserFrosting Resources
- **[UserFrosting Community](https://chat.userfrosting.com)** - Get help from the community
- **[UserFrosting GitHub](https://github.com/userfrosting/)** - Main repository and organization

### Project-Specific Documentation
- **[API Endpoints](docs/api-endpoints.md)** - Complete API reference
- **[Database Schema](docs/database-schema.md)** - Database structure and relationships
- **[Logic System](docs/logic-system.md)** - Content personalization guide

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 🔗 Related Projects

- **Mobile App**: [Guide Wizard Flutter App Repository]
- **Website**: [Guide Wizard Website]

## 📞 Support

For questions and support:
- Create an issue in the GitHub repository
- Check UserFrosting documentation and community
- Review the troubleshooting section above

---

**Built with ❤️ using UserFrosting**
