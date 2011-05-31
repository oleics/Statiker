
/*  */
Statiker.grid.Sites = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'statiker-grid-sites'
        ,url: Statiker.config.connectorUrl
        ,baseParams: {
            action: 'mgr/site/getlist'
        }
        ,fields: [
            'id'
            ,'name'
            ,'context_key'
            ,'write_to_directory'
        ]
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/site/updateFromGrid'
        ,autosave: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 60
        },{
            header: _('statiker.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 150
            ,editor: { xtype: 'textfield' }
        },{
            header: _('statiker.context_key')
            ,dataIndex: 'context_key'
            ,sortable: false
            ,width: 150
            ,editor: {
                xtype: 'statiker-combo-context_key'
                ,renderer: true
            }
        },{
            header: _('statiker.write_to_directory')
            ,dataIndex: 'write_to_directory'
            ,sortable: false
            ,width: 300
            ,editor: { xtype: 'textfield' }
        }]
        ,tbar: [{
            xtype: 'textfield'
            ,id: 'statiker-search-filter'
            ,emptyText: _('statiker.search...')
            ,listeners: {
                'change': {fn:this.search, scope:this}
                ,'render': {
                    fn: function(cmp) {
                        new Ext.KeyMap(cmp.getEl(), {
                            key: Ext.EventObject.ENTER
                            ,fn: function() {
                                this.fireEvent('change',this);
                                this.blur();
                                return true;
                            }
                            ,scope: cmp
                        });
                    }
                    ,scope:this
                }
            }
        },{
            text: _('statiker.site_create')
            ,handler: {
                xtype: 'statiker-window-site-create'
                ,blankValues: true
            }
        },{
            text: _('statiker.build_all')
            //,icon: 'list-items.gif' // icons can also be specified inline
            //,cls: 'x-btn-text-icon'
            //,tooltip: '<b>Quick Tips</b><br/>Icon only button with tooltip'
            ,handler: {
                xtype: 'statiker-window-site-build'
                ,blankValues: true
                ,context_key: ''
            }
        }]
    });
    Statiker.grid.Sites.superclass.constructor.call(this,config)
};
Ext.extend(Statiker.grid.Sites,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,getMenu: function() {
        var m = [{
            text: _('statiker.site_build')
            ,handler: this.buildSite
            // ,handler: {
                // xtype: 'statiker-window-site-build'
                // ,blankValues: true
                // ,context_key: 'test'
            // }
        },'-',{
            text: _('statiker.site_update')
            ,handler: this.updateSite
        },'-',{
            text: _('statiker.site_remove')
            ,handler: this.removeSite
        }];
        this.addContextMenuItem(m);
        return true;
    }
    ,buildSite: function(btn,e) {
        if(!this.buildSiteWindow) {
            this.buildSiteWindow = MODx.load({
                xtype: 'statiker-window-site-build'
                ,blankValues: true
                ,context_key: this.menu.record.context_key
            });
        } else {
            this.buildSiteWindow.setValues({
                context_key: this.menu.record.context_key
            });
            this.buildSiteWindow.context_key = this.menu.record.context_key;
        }
        this.buildSiteWindow.show(e.target);
    }
    ,updateSite: function(btn,e) {
        if(!this.updateSiteWindow) {
            this.updateSiteWindow = MODx.load({
                xtype: 'statiker-window-site-update'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        } else {
            this.updateSiteWindow.setValues(this.menu.record);
        }
        this.updateSiteWindow.show(e.target);
    }
    ,removeSite: function() {
        MODx.msg.confirm({
            title: _('statiker.site_remove')
            ,text: _('statiker.site_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/site/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('statiker-grid-sites',Statiker.grid.Sites);

/*  */
Statiker.combo.ContextKey = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        url: Statiker.config.connectorUrl
        ,baseParams: {
            action: 'mgr/context/getlist'
        }
        ,fields: [
            'key'
        ]
        ,valueField: 'key'
        ,displayField: 'key'
    });
    Statiker.combo.ContextKey.superclass.constructor.call(this,config)
};
Ext.extend(Statiker.combo.ContextKey,MODx.combo.ComboBox,{
});
Ext.reg('statiker-combo-context_key', Statiker.combo.ContextKey);

/*  */
Statiker.window.UpdateSite = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('statiker.site_update')
        ,url: Statiker.config.connectorUrl
        ,baseParams: {
            action: 'mgr/site/update'
        }
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('statiker.name')
            ,name: 'name'
            ,width: 300
        },{
            xtype: 'statiker-combo-context_key'
            ,fieldLabel: _('statiker.context_key')
            ,name: 'context_key'
            ,width: 300
        },{
            xtype: 'textfield'
            ,fieldLabel: _('statiker.write_to_directory')
            ,name: 'write_to_directory'
            ,width: 300
        }]
    });
    Statiker.window.UpdateSite.superclass.constructor.call(this,config);
};
Ext.extend(Statiker.window.UpdateSite,MODx.Window);
Ext.reg('statiker-window-site-update',Statiker.window.UpdateSite);

/*  */
Statiker.window.CreateSite = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('statiker.site_create')
        ,url: Statiker.config.connectorUrl
        ,baseParams: {
            action: 'mgr/site/create'
        }
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('statiker.name')
            ,name: 'name'
            ,width: 300
        },{
            xtype: 'statiker-combo-context_key'
            ,fieldLabel: _('statiker.context_key')
            ,name: 'context_key'
            ,width: 300
        },{
            xtype: 'textfield'
            ,fieldLabel: _('statiker.write_to_directory')
            ,name: 'write_to_directory'
            ,width: 300
        }]
    });
    Statiker.window.CreateSite.superclass.constructor.call(this,config);
};
Ext.extend(Statiker.window.CreateSite,MODx.Window);
Ext.reg('statiker-window-site-create',Statiker.window.CreateSite);

/*  */
Statiker.window.BuildSite = function(config) {
    config = config || {};
    /*  */
    Ext.applyIf(config,{
        title: _('statiker.site_build')
        ,buttons: [{
            text: 'Abort'
            ,disabled: false
            ,handler: function() {
                this.buildSiteRunning = false;
            }
            ,scope:this
        }]
        ,header: true
        ,closable: false
        ,collapsible: false
        ,maximizable: false
        ,minimizable: false
        ,footer: true
        ,modal: true
        ,items: []
    });
    Statiker.window.BuildSite.superclass.constructor.call(this,config);
    /*  */
    this.on('show', function(){this.buildSite()}, this);
};
Ext.extend(Statiker.window.BuildSite, MODx.Window, {
    context_key: ''
    ,resourcesTotal: 1
    ,resourcesOffset: 0
    ,buildSiteRunning: false
    ,buildSite: function() {
        if(!this.buildSiteRunning) {
            //this.loadConsole(Ext.getBody(), '/statiker/build/');
            this.removeAll();
            this.pbar = this.add(new Ext.ProgressBar({
                text:'Initializing...'
            }));
            this.resourcesTotal = 0;
            this.resourcesOffset = 0;
            this.buildSiteRunning = true;
            this.runBuildSite();
        }
    }
    ,runBuildSite: function() {
        if(this.buildSiteRunning) {
            try {
                Ext.Ajax.request({
                    url: Statiker.config.connectorUrl
                    ,params: {
                        action: 'mgr/build/all'
                        ,start: this.resourcesOffset
                        ,limit: 5
                        ,context_key: this.context_key
                        ,register: 'mgr'
                        ,topic: '/statiker/build/'
                        ,show_filename: 0
                    }
                    ,success: function(r) {
                        data = Ext.util.JSON.decode(r.responseText);
                        console.log(data);
                        this.resourcesTotal = data.total;
                        if(data.results.length) {
                            this.resourcesOffset += data.results.length;
                            this.pbar.updateProgress(this.resourcesOffset/this.resourcesTotal, 'Resource ' + this.resourcesOffset + ' of '+this.resourcesTotal+'...');
                        } else {
                            // we are done:
                            this.buildSiteRunning = false;
                            MODx.msg.alert('Success','DONE success!');
                        }
                        this.runBuildSite();
                    }
                    ,failure: function(data) {
                        MODx.msg.alert('Error','Dont know what.');
                        this.buildSiteRunning = false;
                        this.runBuildSite();
                    },
                    scope:this
                });
            } catch(e) {
                console.error(e);
            }
        } else {
            this.remove(this.pbar);
            delete this.pbar;
            this.hide();
            //this.getConsole().fireEvent('complete');
        }
    }
    ,console: null
    ,loadConsole: function(btn,topic) {
    	if (this.console === null) {
            this.console = MODx.load({
               xtype: 'modx-console'
               ,register: 'mgr'
               ,topic: topic
               ,show_filename: 0
            });
        } else {
            this.console.setRegister('mgr', topic);
        }
        this.console.show(btn);
    }
    ,getConsole: function() {
        return this.console;
    }
});
Ext.reg('statiker-window-site-build', Statiker.window.BuildSite);
