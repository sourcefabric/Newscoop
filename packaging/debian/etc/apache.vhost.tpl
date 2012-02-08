<VirtualHost *:80>
      ServerName __SERVER_NAME__
      #ServerAlias www.example.com

      ServerAdmin __SERVER_ADMIN__

      DocumentRoot /usr/share/newscoop
      DirectoryIndex index.php index.html
      Alias /javascript /usr/share/newscoop/javascript/

      <Directory /usr/share/newscoop>
              Options -Indexes FollowSymLinks MultiViews
              AllowOverride All
              Order allow,deny
              Allow from all
      </Directory>
</VirtualHost> 
