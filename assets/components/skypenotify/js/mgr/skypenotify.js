var skypenotify = function (config) {
	config = config || {};
	skypenotify.superclass.constructor.call(this, config);
};
Ext.extend(skypenotify, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('skypenotify', skypenotify);

skypenotify = new skypenotify();