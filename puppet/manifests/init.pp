exec { 'apt-get update':
  path => '/usr/bin',
}

package { 'vim':
  ensure => present,
}

package { "python-software-properties":
  ensure => present,
  require => Exec['apt-get update'],
}

package { "pkg-config":
  ensure => present,
  require => Exec['apt-get update'],
}

package { 'curl':
  ensure => present,
}

file { '/var/www/':
  ensure => 'directory',
}

include nginx, php, mysql, zeromq, zeromq-php, stdlib, composer

class { 'phpunit':
    phar_uri     => 'https://phar.phpunit.de/phpunit.phar',
    install_path => '/usr/local/bin/phpunit',
}

class nginx {

  # Symlink /var/www/coinflip on our guest with 
  # host /path/to/vagrant on our system
  file { '/var/www/coinflip':
    ensure  => 'link',
    target  => '/vagrant',
  }

  # Install the nginx package. This relies on apt-get update
  package { 'nginx':
    ensure => 'present',
    require => Exec['apt-get update'],
  }

  # Make sure that the nginx service is running
  service { 'nginx':
    ensure => running,
    require => Package['nginx'],
  }

  # Add a vhost template
  file { 'vagrant-nginx':
    path => '/etc/nginx/sites-available/coinflip.dev',
    ensure => file,
    require => Package['nginx'],
      source => 'puppet:///modules/nginx/coinflip.dev',
  }

  # Disable the default nginx vhost
  file { 'default-nginx-disable':
    path => '/etc/nginx/sites-enabled/default',
    ensure => absent,
    require => Package['nginx'],
  }

  # Symlink our vhost in sites-enabled to enable it
  file { 'vagrant-nginx-enable':
    path => '/etc/nginx/sites-enabled/coinflip.dev',
    target => '/etc/nginx/sites-available/coinflip.dev',
    ensure => link,
    notify => Service['nginx'],
    require => [
      File['vagrant-nginx'],
      File['default-nginx-disable'],
    ],
  }
}

class php {

  exec { 'add-apt-repository ppa:ondrej/php5-5.6':
    command => '/usr/bin/add-apt-repository ppa:ondrej/php5-5.6',
    require => Package["python-software-properties"],
  }

  exec { 'apt-get update for ondrej/php5':
        command => '/usr/bin/apt-get update',
        before => Package['php5-fpm','php5-cli', 'php5-mysql', 'php5-dev', 'php-pear'],
        require => Exec['add-apt-repository ppa:ondrej/php5-5.6'],
  }
    
  # Install the php5-fpm and php5-cli and php5-mysql packages
  package { ['php5-fpm','php5-cli', 'php5-mysql', 'php5-dev', 'php-pear']:
    ensure => present,
    require => Exec['apt-get update'],
  }

  # Make sure php5-fpm is running
  service { 'php5-fpm':
    ensure => running,
    require => Package['php5-fpm'],
  }
}

class mysql {

  # Install mysql
  package { ['mysql-server']:
    ensure => present,
    require => Exec['apt-get update'],
  }

  # Run mysql
  service { 'mysql':
    ensure  => running,
    require => Package['mysql-server'],
  }

  # Use a custom mysql configuration file
  file { '/etc/mysql/my.cnf':
    source  => 'puppet:///modules/mysql/my.cnf',
    require => Package['mysql-server'],
    notify  => Service['mysql'],
  }

  # We set the root password here
  exec { 'set-mysql-password':
    unless  => 'mysqladmin -uroot -proot status',
    command => "mysqladmin -uroot password aberdeen",
    path    => ['/bin', '/usr/bin'],
    require => Service['mysql'];
  }
}

class zeromq {
    Package { ensure => "installed" }

    $apt_base = "/etc/sources.list.d/chris-lea"

    exec { "zeromq-repo" :
        command => "/usr/bin/add-apt-repository ppa:chris-lea/zeromq",
        creates => "${apt_base}/zeromq-lucid.list",
        require => Package["python-software-properties"]
    }

    exec { "libpgm-repo" :
        command => "/usr/bin/add-apt-repository ppa:chris-lea/libpgm",
        creates => "${apt_base}/libpgm-lucid.list",
        require => Package["python-software-properties"]
    }

    $required_execs = ["zeromq-repo", "libpgm-repo"]

    exec { "apt-ready" :
        command => "/usr/bin/apt-get update",
        require => Exec[$required_execs]
    }

    package { [ "libzmq-dev" ] :
        require => Exec["apt-ready"]
    }
}

class zeromq-php{
	exec { "add-zeromq-to-php" :
		command => "/usr/bin/pecl install zmq-beta",
	    require => [Class['zeromq'], Package['php5-fpm','php5-cli', 'php5-dev', 'php-pear']],
	}
}

file_line { 'Add zmq extension to fpm php.ini':
  notify  => Service['php5-fpm'],
  path => '/etc/php5/fpm/php.ini',  
  line => 'extension=zmq.so',
  require => Class['zeromq-php'],
}

file_line { 'Add zmq extension to cli php.ini':
  path => '/etc/php5/cli/php.ini',  
  line => 'extension=zmq.so',
  require => Class['zeromq-php'],
}