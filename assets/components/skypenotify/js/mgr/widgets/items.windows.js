skypenotify.window.CreateItem = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'skypenotify-item-window-create';
	}
	Ext.applyIf(config, {
		title: _('skypenotify_item_create'),
		width: 550,
		autoHeight: true,
		url: skypenotify.config.connector_url,
		action: 'mgr/item/create',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	skypenotify.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(skypenotify.window.CreateItem, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('skypenotify_item_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
		}, {
			xtype: 'textarea',
			fieldLabel: _('skypenotify_item_description'),
			name: 'description',
			id: config.id + '-description',
			height: 150,
			anchor: '99%'
		}, {
			xtype: 'xcheckbox',
			boxLabel: _('skypenotify_item_active'),
			name: 'active',
			id: config.id + '-active',
			checked: true,
		}];
	}

});
Ext.reg('skypenotify-item-window-create', skypenotify.window.CreateItem);


skypenotify.window.UpdateItem = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'skypenotify-item-window-update';
	}
	Ext.applyIf(config, {
		title: _('skypenotify_item_update'),
		width: 550,
		autoHeight: true,
		url: skypenotify.config.connector_url,
		action: 'mgr/item/update',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	skypenotify.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(skypenotify.window.UpdateItem, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'hidden',
			name: 'id',
			id: config.id + '-id',
		}, {
			xtype: 'textfield',
			fieldLabel: _('skypenotify_item_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
		}, {
			xtype: 'textarea',
			fieldLabel: _('skypenotify_item_description'),
			name: 'description',
			id: config.id + '-description',
			anchor: '99%',
			height: 150,
		}, {
			xtype: 'xcheckbox',
			boxLabel: _('skypenotify_item_active'),
			name: 'active',
			id: config.id + '-active',
		}];
	}

});
Ext.reg('skypenotify-item-window-update', skypenotify.window.UpdateItem);