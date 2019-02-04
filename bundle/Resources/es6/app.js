import $ from 'jquery';
import { NgUiInit } from '@netgen/admin-ui';

window.jQuery = $;

window.addEventListener('load', () => {
  NgUiInit();
});
