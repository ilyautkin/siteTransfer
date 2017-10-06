siteTransfer.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'sitetransfer-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('sitetransfer') + '</h2>',
			cls: '',
			style: {margin: '15px 0'}
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
                title: _('sitetransfer_update'),
				layout: 'anchor',
				items: [{
					html: _('sitetransfer_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'sitetransfer-export-panel',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	siteTransfer.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(siteTransfer.panel.Home, MODx.Panel);
Ext.reg('sitetransfer-panel-home', siteTransfer.panel.Home);
