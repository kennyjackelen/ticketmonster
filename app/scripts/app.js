(function(document) {
  'use strict';

  var _mainDrawerPanel;
  var _navDrawer;
  var _ticketMonster;
  var _toggleButton;

  var eventList;

  function toggleSideNav() {
    _mainDrawerPanel.togglePanel();
  }

  function showHideToggleButton() {
    if ( _mainDrawerPanel.narrow ) {
      $(_toggleButton).removeClass('widescreen');
    }
    else {
      $(_toggleButton).addClass('widescreen');
    }
  }

  function initializeData() {

    eventList = [];
    eventList.push(
        {
          event_id: '00004CA5A27CBB03',
          name: 'Drake vs Lil Wayne',
          date: 'Tue, Aug 12, 2014 07:00 PM',
          venue_name: 'Saratoga Performing Arts Center',
          venue_city: 'Saratoga Springs',
          venue_state: 'NY',
          my_price: 50,
          price_data: {
            min: 54.15,
            first_q: 69.99,
            median: 107.88,
            third_q: 183.70,
            max: 254.70
          }
        }
      );
    eventList.push(
        {
          event_id: '1D004C79F66E9D0D',
          name: 'Fleetwood Mac: On With The Show',
          date: 'Tue, Oct 7, 2014 08:00 PM',
          venue_name: 'Madison Square Garden',
          venue_city: 'New York',
          venue_state: 'NY',
          my_price: 75,
          price_data: {
            min: 163.45,
            first_q: 238.89,
            median: 321.87,
            third_q: 412.39,
            max: 2379.30
          }
        }
      );
  }

  function closeDrawer( selectEvent ) {
    if ( _mainDrawerPanel.narrow ) {
      _mainDrawerPanel.closeDrawer();
    }
  }

  function polymerReady() {

    $.getJSON( 'data/events.json', digestEventList );

    _mainDrawerPanel = document.querySelector('#mainDrawerPanel');
    _navDrawer = document.querySelector('#navDrawer');
    _ticketMonster = document.querySelector('#ticketMonster');
    _toggleButton = document.querySelector('#navToggle');

    _navDrawer.addEventListener( 'item-selected', closeDrawer );

    if ( _mainDrawerPanel.narrow ) {
      $(_toggleButton).removeClass('widescreen');
    }

    _mainDrawerPanel.addEventListener( 'core-responsive-change', showHideToggleButton );

    _toggleButton.addEventListener( 'click', toggleSideNav );

  }

  function digestEventList( eventData ) {
    eventList = eventData;
    _navDrawer.eventList = eventList;
    _ticketMonster.eventList = eventList;
  }

  document.addEventListener('polymer-ready', polymerReady);

// wrap document so it plays nice with other libraries
// http://www.polymer-project.org/platform/shadow-dom.html#wrappers
})(wrap(document));
