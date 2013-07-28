set :application, "Blog"
set :repository,  "https://github.com/wjzijderveld/blog.git"

set :scm, :git # You can set :scm explicitly or Capistrano will make an intelligent guess based on known version control directory names
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "vps1891.directvps.nl"                          # Your HTTP server, Apache/etc
role :app, "vps1891.directvps.nl"                          # This may be the same as your `Web` server
# role :db,  "your primary db-server here", :primary => true # This is where Rails migrations will run
# ole :db,  "your slave db-server here"

set :keep_releases, 2
# if you want to clean up old releases on each deploy uncomment this:
after "deploy:restart", "deploy:cleanup"

set :shared_children, []
set :public_children, []
# if you're still using the script/reaper helper you will need
# these http://github.com/rails/irs_process_scripts

set :deploy_via, :remote_cache
set :deploy_to, "/var/customers/webs/wjzijderveld/blog.willem-jan.net"
set :user, "wjzijderveld"
set :use_sudo, false


# If you are using Passenger mod_rails uncomment this:
namespace :deploy do
  task :start do ; end
  task :stop do ; end
  task :restart, :roles => :app, :except => { :no_release => true } do
    run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
  end
end