#
# Cookbook Name:: newscoop
# Recipe:: default
#

execute "apt-get update"

pkgs = [
    "apache2",
    "mysql-server",
    "php5", "php5-dev", "php5-cli", "php-pear", "php5-gd",
    "php5-mysql", "libapache2-mod-php5",
    "php-apc",
    "git",
    "curl"
]

pkgs.each do |pkg|
    package pkg do
        action :install
    end
end

service "apache2" do
    supports [:restart, :reload, :status]
    action :enable
end

execute "a2enmod rewrite"

template "/etc/apache2/sites-available/default" do
    source "default-site.erb"
    owner "www-data"
    group "www-data"
    mode 0644
    notifies :restart, "service[apache2]"
end

execute "curl -s https://getcomposer.org/installer | php" do
    cwd "/vagrant/"
end

execute "php composer.phar install" do
    cwd "/vagrant/"
end

service "apache2" do
    action :restart
end
