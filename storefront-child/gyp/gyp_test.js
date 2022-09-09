console.log('gyp_test.js Loaded.');
jQuery(document).ready(function ($) {
  $('.single_variation_wrap').on('show_variation', function (event, variation) {
    console.log(variation.variation_id);
  });
});
