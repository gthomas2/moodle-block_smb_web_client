M.swc_config={};

M.swc_config.init=function(Y, obj){
    
    window.Y=Y; // nasty but if I don't do this then Y.Lang won't work for datatable_quickedit plugin !!!!!




    Y.Get.script([obj.blockwww+'js/datatable_quickedit.js?r5'], {
        onSuccess:function(){
            
            var clog=function(msg){
                if (window.console && window.console.log){
                    console.log(msg);
                }
            }

            var tmpa=Y.version.split('.');
            var verno=tmpa[0];
            var vpoint='';
            for (var v=1; v<tmpa.length; v++){
                vpoint+=''+tmpa[v];       
            }
            verno+='.'+vpoint;

            // get share details
            var shrta=Y.one("#id_s_smb_web_client_shares");
            var shrcontent=(shrta.getContent());
            var shrarr=[];
            if (shrcontent!=''){
                tmpa=shrcontent.split("\n");
                for (var v=0; v<tmpa.length; v++){
                    var shrrow=tmpa[v];
                    var tmpb=shrrow.split("|");
                    var shrname=tmpb[0].trim();
                    var tmpc=tmpb[1].split(":");
                    var shrpath=tmpc[0].trim();
                    var filters='';
                    if (tmpc.length>1){
                        for (var f=1; f<tmpc.length; f++){
                            filters+=filters=='' ? '' : ','+"\n";
                            filters+=tmpc[f];
                        }
                    }
                    var actions='<div class="actions"><a class="ico ico_del" id="delshare_'+v+'" href="#" onclick="return(false);">&nbsp;</a></div>';

                    shrarr.push({actions:actions, id:v, name:shrname, path:shrpath, filters:filters});
                }

            }   

            if (shrcontent!=''){
                // create container for datatable
                var tabcont=Y.Node.create('<div id="sharetablecont"><div id="controls"><button onclick="return false;" id="add">Add</button><button onclick="return false;" id="start">Edit</button><button onclick="return false;" id="update" style="display:none;">Update</button><button onclick="return false;" id="cancel" style="display:none;">Cancel</button></div><div id="sharetable"></div></div>');
                shrta.insert(tabcont,'after');

                var ashrs=Y.one('#admin-shares');
                ashrs.one('.form-defaultinfo').setStyle('display','none');
                ashrs.one('.form-description').setStyle('display','none');
                shrta.setStyle('display','none');

                if (verno>=3.41 && verno<3.5){

                    // create columnset
                    var cols = [{key:"actions",label:""},{key:"name", label:"Share Name", emptyCellValue:'-', quickEdit: true},{key:"path", label:"Share", emptyCellValue:'-', quickEdit: true},{key:"filters", label:"Filter", emptyCellValue:'-', quickEdit: true}];

                    var datasrc=new Y.DataSource.Function({
                        source:function(request){return (shrarr)}
                    });


                    datasrc.plug(Y.Plugin.DataSourceJSONSchema, {
                        schema: {
                            resultFields: ["actions","name", "path", "filters"]
                        }               
                    });



                    // create datatable and render
                    var table = new Y.DataTable.Base({
                        columnset: cols
                    });
                    table.plug(Y.Plugin.DataTableDataSource, {
                        datasource: datasrc
                    });
                    table.plug(Y.Plugin.DataTableQuickEdit, {changesAlwaysInclude: ['id']});

                    datasrc.after('data',function(e){
                        // add action events
                        for (var s in shrarr){
                            var shr=shrarr[s];

                            (function(s, shr){
                                Y.one("#delshare_"+shr.id).on('click', function(){
                                    for (var c in shrarr){
                                        var cshr=shrarr[c];
                                        if (cshr.id==shr.id){
                                            //clog('removing share '+shr.id+' from position '+c);
                                            shrarr.splice(c,1);
                                            table.render('#sharetable');
                                            table.datasource.load({request:null});

                                            return;
                                        }
                                    }                            
                                });
                            })(s, shr);

                        }
                    });            

                    table.render('#sharetable');



                    table.datasource.load({request:null});

                    var start  = Y.one('#start');
                    var add  = Y.one('#add');
                    var update   = Y.one('#update');
                    var cancel = Y.one('#cancel');

                    add.on('click', function(){
                       //console.log(datasrc);

                       var actions='<div class="actions"><a class="ico ico_del" id="delshare_'+(shrarr.length)+'" href="#" onclick="return(false);">&nbsp;</a></div>';

                       shrarr.push({actions:actions, id:shrarr.length, name:'',path:'',filters:''});
                       table.render('#sharetable');
                       table.datasource.load({request:null});
                    });

                    start.on('click', function()
                    {
                        table.qe.start();
                        start.set('disabled', true);
                        update.show();
                        cancel.show();
                    });

                    function finish()
                    {
                        table.qe.cancel();
                        start.set('disabled', false);
                        update.hide();
                        cancel.hide();
                    }

                    update.on('click', function()
                    {
                        var changes = table.qe.getChanges();
                        if (changes)
                        {

                            clog(changes);


                            for (var i=0; i<changes.length; i++)
                            {
                                var change = changes[i];
                                var rec    = null;
                                for (var j in shrarr)
                                {

                                    clog('checking sharr id '+shrarr[j].id+' against changed id '+change.id);

                                    if (shrarr[j].id === change.id)
                                    {
                                        rec = shrarr[j];
                                        break;
                                    }
                                }

                                if (rec)
                                {
                                    for (var key in change)
                                    {
                                        if (change.hasOwnProperty(key))
                                        {
                                                rec[key] = change[key];
                                        }
                                    }
                                }
                            }

                            finish();
                        }
                    });

                    cancel.on('click', function ()
                    {
                        finish();
                    });






                } else if (verno>=3.5){

                }
            }


        }
    });
    


 }