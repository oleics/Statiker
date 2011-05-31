
/*  */
Statiker.grid.Files = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    Ext.applyIf(config,{
        id: 'statiker-grid-files'
        ,url: Statiker.config.connectorUrl
        ,baseParams: {
            action: 'mgr/file/getlist'
        }
        ,fields: [
            'resource'
            ,'context_key'
            ,'static_path'
            ,'static_url'
            ,'static_size'
            ,'static_size_compressed'
            ,'static_size_gzencoded'
            ,'bytes_written'
            ,'bytes_written_gzencoded'
        ]
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/file/updateFromGrid'
        ,autosave: true
        ,sm: this.sm
        ,columns: [this.sm,{
            header: _('id')
            ,dataIndex: 'resource'
            ,sortable: true
            ,width: 60
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
            header: _('statiker.static_path')
            ,dataIndex: 'static_path'
            ,sortable: false
            ,width: 300
            ,editor: { xtype: 'textfield' }
        },{
            header: _('statiker.static_url')
            ,dataIndex: 'static_url'
            ,sortable: false
            ,width: 200
            ,editor: { xtype: 'textfield' }
        },{
            header: _('statiker.static_size')
            ,dataIndex: 'static_size'
            ,sortable: false
            ,width: 60
            ,editor: { xtype: 'textfield' }
        },{
            header: _('statiker.static_size_compressed')
            ,dataIndex: 'static_size_compressed'
            ,sortable: false
            ,width: 60
            ,editor: { xtype: 'textfield' }
        },{
            header: _('statiker.static_size_gzencoded')
            ,dataIndex: 'static_size_gzencoded'
            ,sortable: false
            ,width: 60
            ,editor: { xtype: 'textfield' }
        },{
            header: _('statiker.bytes_written')
            ,dataIndex: 'bytes_written'
            ,sortable: false
            ,width: 60
            ,editor: { xtype: 'textfield' }
        },{
            header: _('statiker.bytes_written_gzencoded')
            ,dataIndex: 'bytes_written_gzencoded'
            ,sortable: false
            ,width: 60
            ,editor: { xtype: 'textfield' }
        }]
        ,tbar: [{
            xtype: 'textfield'
            ,id: 'statiker-search-file-filter'
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
            text: _('statiker.file_bulk_actions')
            ,menu: this.getBatchMenu()
        }]
    });
    Statiker.grid.Files.superclass.constructor.call(this,config)
};
Ext.extend(Statiker.grid.Files,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,getMenu: function() {
        var m = [{
            text: _('statiker.file_remove')
            ,handler: this.removeFile
        }];
        this.addContextMenuItem(m);
        return true;
    }
    ,removeFile: function() {
        MODx.msg.confirm({
            title: _('statiker.file_remove')
            ,text: _('statiker.file_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/file/remove'
                ,resource: this.menu.record.resource
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
    
    ,getSelectedAsList: function() {
        var sels = this.getSelectionModel().getSelections();
        if (sels.length <= 0) return false;

        var cs = '';
        for (var i=0;i<sels.length;i++) {
            cs += ','+sels[i].data.resource;
        }
        cs = cs.substr(1);
        return cs;
    }
    ,batchAction: function(act,btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/file/batch'
                ,files: cs
                ,batch: act
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                    /*
                       var t = Ext.getCmp('modx-resource-tree');
                       if (t) { t.refresh(); }
                    */
                },scope:this}
            }
        });
        return true;
    }
    ,getBatchMenu: function() {
        var bm = [];
        bm.push({
            text: _('statiker.files_remove')
            ,handler: function(btn,e) {
                this.batchAction('remove',btn,e);
            }
            ,scope: this
        });
        return bm;
    }
});
Ext.reg('statiker-grid-files',Statiker.grid.Files);
