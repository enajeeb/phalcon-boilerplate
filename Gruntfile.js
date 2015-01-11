//
// Minify, Concat Javascript and CSS for Production deployment
//
module.exports = function(grunt) {
    
    "use strict";

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        
        // Wipe out previous builds
        clean: {
            build: [
                "public/css/prod/output-main.css",
                "public/css/prod/output-login.css",
                "public/css/prod/output-main.min.css",
                "public/css/prod/output-login.min.css",
                "public/css/prod/<%= pkg.name %>.min.css",
                "public/css/prod/<%= pkg.name %>.login.min.css",
                "public/js/prod/output-main.js",
                "public/js/prod/output-login.js",
                "public/js/prod/<%= pkg.name %>.min.js",
                "public/js/prod/<%= pkg.name %>.login.min.js"
            ],
            release: [
                "public/css/prod/output-main.css",
                "public/css/prod/output-login.css",
                "public/css/prod/output.min.css",
                "public/css/prod/output.login.min.css",
                "public/js/prod/output-main.js",
                "public/js/prod/output-login.js"
            ]
        },

        // minify css
        cssmin: {
            main: {
                options: {
                    banner: '\n/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                    '<%= grunt.template.today("dddd, mmmm dS, yyyy, h:MM:ss TT") %> */\n'
                },
                files: {
                    "public/css/prod/output.min.css": ["public/css/prod/output-main.css"]
                }
            },
            login: {
                options: {
                    banner: '\n/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                    '<%= grunt.template.today("dddd, mmmm dS, yyyy, h:MM:ss TT") %> */\n'
                },
                files: {
                    "public/css/prod/output.login.min.css": ["public/css/prod/output-login.css"]
                }
            }
        },

        // minify, obfuscate js
        uglify: {
            main: {
                options: {
                    // mangle: {toplevel: true},
                    // squeeze: {dead_code: false},
                    // codegen: {quote_keys: true},
                    banner: '\n/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                    '<%= grunt.template.today("dddd, mmmm dS, yyyy, h:MM:ss TT") %> */\n'
                },
                files: {
                    'public/js/prod/output-main.js': [
                        'public/js/template/app.js', 
                        'public/js/app/*.js'
                    ]
                }
            },
            login: {
                options: {
                    // mangle: {toplevel: true},
                    // squeeze: {dead_code: false},
                    // codegen: {quote_keys: true},
                    banner: '\n/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
                    '<%= grunt.template.today("dddd, mmmm dS, yyyy, h:MM:ss TT") %> */\n'
                },
                files: {
                    'public/js/prod/output-login.js': [
                        'public/js/template/app.js', 
                        'public/js/app/*.js', 
                        '!public/js/app/app-main.js'
                    ]
                }
            }
        },

        concat: {
            jsMain: {
                src: [
                    'public/js/libs/jquery-2.1.0.min.js',
                    'public/js/libs/bootstrap.min.js',
                    'public/js/libs/jquery.sparkline.min.js',
                    'public/js/libs/jarvis.widget.min.js',
                    'public/js/libs/SmartNotification.min.js',
                    'public/js/prod/output-main.js'
                ],
                dest: 'public/js/prod/<%= pkg.name %>.min.js'
            },
            jsLogin: {
                src: [
                    'public/js/libs/jquery-2.1.0.min.js',
                    'public/js/libs/bootstrap.min.js',
                    'public/js/libs/jquery.sparkline.min.js',
                    'public/js/libs/jarvis.widget.min.js',
                    'public/js/libs/SmartNotification.min.js',
                    'public/js/prod/output-login.js'
                ],
                dest: 'public/js/prod/<%= pkg.name %>.login.min.js'
            },
            appCss: {
                src: [
                    'public/css/app/app-main.css'
                ],
                dest: 'public/css/prod/output-main.css'
            },
            loginCss: {
                src: [
                    'public/css/app/app-main.css'
                ],
                dest: 'public/css/prod/output-login.css'
            },
            cssRelease: {
                src: [
                    'public/css/template/bootstrap.min.css',
                    'public/css/template/font-awesome.min.css',
                    'public/css/template/smartadmin-production.min.css',
                    'public/css/prod/output.min.css'
                ],
                dest: 'public/css/prod/<%= pkg.name %>.min.css'
            },
            cssLoginRelease: {
                src: [
                    'public/css/template/bootstrap.min.css',
                    'public/css/template/font-awesome.min.css',
                    'public/css/template/smartadmin-production.min.css',
                    'public/css/prod/output.login.min.css'
                ],
                dest: 'public/css/prod/<%= pkg.name %>.login.min.css'
            }
        },

        // run this using "grunt watch". 
        watch: {
            files: ['public/css/*.css', 'public/js/*.js'],
            tasks: ['default']
        }

    });

    grunt.loadNpmTasks("grunt-contrib-clean");
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // grunt.registerTask('default', ['clean', 'concat']);
    grunt.registerTask('default', [
        'clean:build', 
        'uglify:main', 
        'uglify:login', 
        'concat:jsMain', 
        'concat:jsLogin', 
        'concat:appCss', 
        'concat:loginCss', 
        'cssmin:main', 
        'cssmin:login', 
        'concat:cssRelease', 
        'concat:cssLoginRelease', 
        'clean:release'
    ]);
};