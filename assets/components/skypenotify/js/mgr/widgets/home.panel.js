skypenotify.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'skypenotify-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('skypenotify') + '</h2>',
			cls: '',
			style: {margin: '15px 0'}
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
				title: _('skypenotify_items'),
				layout: 'anchor',
				items: [{
					html: _('skypenotify_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'skypenotify-grid-items',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	skypenotify.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(skypenotify.panel.Home, MODx.Panel);
Ext.reg('skypenotify-panel-home', skypenotify.panel.Home);
