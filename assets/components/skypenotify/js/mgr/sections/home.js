skypenotify.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'skypenotify-panel-home', renderTo: 'skypenotify-panel-home-div'
		}]
	});
	skypenotify.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(skypenotify.page.Home, MODx.Component);
Ext.reg('skypenotify-page-home', skypenotify.page.Home);