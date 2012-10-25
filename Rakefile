task :default => [:build]

namespace "generate" do
    task :proxy do
        sh "php newscoop/scripts/doctrine.php orm:generate-proxies"
    end
end

namespace "composer" do
    file "composer.phar" do |f|
        sh "curl -s https://getcomposer.org/installer | php"
    end

    task :install => ["composer.phar"] do
        sh "php composer.phar install"
    end
end

task :build do
    directory "build"
end

task :test => ["composer:install"] do
    sh "phpunit -c newscoop/tests/phpunit.xml"
end
