'use strict';
module.exports = function(grunt) {
  // Load all tasks
  require('load-grunt-tasks')(grunt);
  // Show elapsed time
  require('time-grunt')(grunt);

  var pluginList = [
    'bower_components/leaflet/dist/leaflet.js',
    'bower_components/leaflet-heat/index.js',
    'js/trees-id-map.js'
  ];

  var buildList = [
    'bower_components/leaflet/dist/leaflet.js',
    'bower_components/leaflet-heat/index.js',
    'js/trees-id-map.js',
    'js/main.js',
  ];

  grunt.initConfig({
    sass: {
      dist: {
        options: {
          style: 'expanded'
        },
        files: {
          'css/trees-id.css': 'sass/main.scss'
        }
      }
    },
    cssmin: {
        dist: {
            files: {
                'css/trees-id.min.css': [
                    'css/trees-id.css'
                ]
            }
        }
    },
    concat: {
      options: {
        separator: ';',
      },
      plugins: {
        src: [pluginList],
        dest: 'js/trees-id-plugin.js',
      },
    },
    uglify: {
      plugins: {
        files: {
          'js/trees-id.min.js': [buildList]
        }
      },
    },
    modernizr: {
      build: {
        devFile: 'bower_components/modernizr/modernizr.js',
        outputFile: 'js/modernizr.min.js',
        files: {
          'src': [
            ['js/trees-id.min.js'],
            // ['css/main.min.css']
          ]
        },
        uglify: true,
        parseFiles: true
      }
    },
    watch: {
      css: {
        files: '**/*.scss',
        tasks: ['sass']
      },
      js: {
        files: [
          pluginList,
          '<%= jshint.all %>'
        ],
        tasks: ['concat']
      },
      livereload: {
        // Browser live reloading
        // https://github.com/gruntjs/grunt-contrib-watch#live-reloading
        options: {
          livereload: true
        },
        files: [
          'css/main.scss',
          'css/trees-id.css',
          'js/main.js',
          '*/*.php',
          '*.php',
          '*.html'
        ]
      }
    }
  });

  // Register tasks yo
  grunt.registerTask('default', [
    'dev'
  ]);
  grunt.registerTask('dev', [
    'sass',
    'cssmin',
    'concat'
  ]);
  grunt.registerTask('build', [
    'sass',
    'cssmin',
    'uglify',
    'modernizr',
  ]);
};
