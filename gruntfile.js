var autoprefixer = require('autoprefixer');
module.exports = function(grunt) {

grunt.initConfig({
  pkg: grunt.file.readJSON('package.json'),

  // deployment settings
  settings:{
    production: {

    }
  },

  //
  rsync: {
    options: {
        args: ["--verbose --times"],
        exclude: [".git*","*.scss","node_modules"],
        recursive: true
    },
    'build': {
        options: {
            dest: "build",
            src: ".",
            exclude: ['site/accounts','content','cache', 'thumbs',".git*","*.scss","node_modules",'.sass-cache', 'gruntfile.js','.DS_Store', '**/.DS_Store', '**/Thumbs.db', 'package.json', 'package-lock.json']
        }
    },
  },

  // build
  sass: {
    dist: {
      options: {
        style: 'compressed'
      },
      files: {
        'assets/css/main.css': 'assets/sass/main.scss',
        'assets/css/panel.css': 'assets/sass/panel.scss',
      }
    } 
  },
  postcss: {
      options: {
        processors: [
          autoprefixer({ browsers: ['> 1%'] }),
        ],
      },
      dist: {
          src: 'assets/css/*.css'
      }
    },
    // concat: {
    //   options: {
    //     separator: '\n;',
    //   },
    //   dist: {
    //     src: ['node_modules/jquery/dist/jquery.min.js', 'assets/js/script.js'],
    //     dest: 'assets/js/main.js',
    //   },
    // },
    // jshint: {
    //   files: ['assets/js/script.js'],
    //   options: {
    //     laxbreak: true,
    //   },
    // },

  // local dev
  watch: {
    css: {
      files: ['assets/sass/*.scss'],
      tasks: ['sass']
    },
  },
  browserSync: {
    dev: {
      bsFiles: {
        src: [
              '**/*.php',
              '**/*.css',
              '**/*.js'
             ]
      },
      options: {
        proxy: 'localhost:8010',
        port: '9000',
        open: true,
        watchTask: true,
      }
    }
  },
  php: {
    dev: {
      options: {
        port: 8010,
        base: '.',
        ini: '/Applications/MAMP/conf/php7.1.1/php.ini'
      }
    }
  }
  
});

grunt.loadNpmTasks('grunt-contrib-sass');
grunt.loadNpmTasks('grunt-contrib-watch');
grunt.loadNpmTasks('grunt-browser-sync');
grunt.loadNpmTasks('grunt-php');
grunt.loadNpmTasks('grunt-sftp-deploy');
grunt.loadNpmTasks('grunt-rsync');
grunt.loadNpmTasks('grunt-postcss');

grunt.registerTask('default', ['php','browserSync','watch']);
grunt.registerTask('build', ['sass', 'postcss']); 
grunt.registerTask('deploy', ['build', 'rsync:deploy']);

grunt.registerTask('pull-content', ['rsync:pull-content']); 
grunt.registerTask('push-content', ['rsync:push-content']); 
};