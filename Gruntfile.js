module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    uglify: {
      my_target: {
        files: {
        'js/build/user.min.js' : ['js/user/*.js'],
        'js/build/sfl.min.js' : ['js/sfl/*.js'],
        'js/build/manager.min.js' : ['js/manager/*.js']
        }
      }
    }
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // Default task(s).
  grunt.registerTask('default', ['uglify']);

};
