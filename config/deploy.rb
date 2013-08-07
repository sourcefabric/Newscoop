set :application, "newscoop-test.sourcefabric.org"
set :repository,  "git://github.com/sourcefabric/Newscoop.git"
set :branch, 'stable'

set :scm, :git # You can set :scm explicitly or Capistrano will make an intelligent guess based on known version control directory names
set :runner, "root"
set :user, "root"
set :use_sudo, false

server "newscoop-test.sourcefabric.org", :app, :web, :db, :primary => true
set :deploy_to, "/var/www/#{application}"
set :deploy_via, :remote_cache
set :keep_releases, 3
set :normalize_asset_timestamps, false

after "deploy:restart", "deploy:install", "deploy:cleanup"

set :shared_children, ['newscoop/conf', 'newscoop/public/pdf', 'newscoop/public/videos', 'newscoop/public/files', 'newscoop/images', 'newscoop/themes', 'newscoop/vendor', 'newscoop/backup', 'newscoop/plugins']

namespace :deploy do
    desc "run composer install and ensure all dependencies are installed"
    task :install do
        run "cd #{current_path}/newscoop && curl -sS https://getcomposer.org/installer | php"
        run "cd #{current_path}/newscoop && php composer.phar install"
        run "cd #{current_path}/newscoop && php composer.phar dump-autoload --optimize"
        run "setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX #{current_path}/newscoop"
        run "setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX #{current_path}/newscoop"
        run "cd #{current_path}/newscoop chmod -R 777 conf/ themes/ backup/ images/ images/thumbnails/ public/files public/videos"
    end
end