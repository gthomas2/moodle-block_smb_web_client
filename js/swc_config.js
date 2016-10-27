M.swc_config={initialised:false, called:0};

M.swc_config.init=function(Y, obj){
    
    M.swc_config.called++; // log number of times called
    
    if (M.swc_config.initialised){
        return; // already initialised
    }
    M.swc_config.initialised=true;
    
    //window.Y=Y; // nasty but if I don't do this then Y.Lang won't work for datatable_quickedit plugin !!!!!
            
    /**
     * log to console if console available
     * @param int msg
     * @return void
     */
    var clog=function(msg){
        if (console && console.log){
            console.log(msg);
        }
    }
    
    /**
     * get share actions for specific shareid
     */
    var shareactions=function(shrid){
        var actions='<div class="actions">';
        actions+="\n\t"+'<a class="ico ico_del" id="delshare_'+shrid+'" href="#" onclick="return(false);">&nbsp;</a>';
        actions+="\n\t"+'<a class="ico ico_edit" id="editshare_'+shrid+'" href="#" onclick="return(false);">&nbsp;</a>';
        actions+="\n"+'</div>'; 
        return (actions);
    }
    
    /**
     * get position of share
     * @param object shr
     * @return boolean|integer (false if not found)
     */
    var shareindex=function(shr){
        for (var c in shrarr){
            var cshr=shrarr[c];
            if (cshr.id==shr.id){
                return (c);
            }
        }         
        return (false);
    }
    
    /**
     * delete share from table
     * @param object shr
     * @return void
     */
    var deleteshare=function(shr){
        var c=shareindex(shr);
        if (c!==false){
            shrarr.splice(c,1);
            table.render('#sharetable');
            table.datasource.load({request:null});
            return;
        }
    }
    
    /**
     * edit share
     * @param object shr
     * @return void
     */
    var editshare=function(shr){
        var shridx=shareindex(shr);
        if (shridx!==false){
            var shrrow=shrarr[shridx];
            var msg='<div class="message">';

            msg+='<div><label for="editshrname">Share Name</label>';
            msg+='<input type="text" id="editshrname" name="editshrname" value="'+shrrow.name+'" /></div>';
            msg+='<div><label for="editshrpath">Path</label>';
            msg+='<input type="text" id="editshrpath" name="editshrpath" value="'+shrrow.path+'" /></div>';
            msg+='<div><label for="editshrfilters">Filters</label>';
            msg+='<input type="text" id="editshrfilters" name="editshrfilters" value="'+shrrow.filters+'" /></div>';            
            msg+='</div>';
            
            var rmain=Y.DOM.byId('region-main');
            var dwidth=0;
            if (rmain){
                dwidth=rmain.parentNode.parentNode.parentNode.parentNode.offsetWidth*0.75;
            } else {
                dwidth=parseInt(Y.one("body").get("winWidth"));
            }
            if (dwidth<400){
                dwidth=400;
            }
            
            var dia_edit_smbclient = new Y.Panel({
                contentBox : Y.Node.create('<div id="dialog" />'),
                bodyContent: msg,
                headerContent: 'Edit Share',
                width      : dwidth,
                zIndex     : 6,
                centered   : true,
                modal      : true, // modal behavior
                render     : 'body',
                visible    : true,
                title      : 'Edit Share',
                buttons: [
                    {
                        value: "OK",

                        // The `action` property takes the Function that should be
                        // executed on a click event.
                        action: function(e) {
                            var name=Y.Node.one('#editshrname').get('value');
                            var path=Y.Node.one('#editshrpath').get('value');
                            var filters=Y.Node.one('#editshrfilters').get('value');
                            shrrow.name=name;
                            shrrow.path=path;
                            shrrow.filters=filters;
                            table.datasource.load({request:null});
                            dia_edit_smbclient.hide();
                            dia_edit_smbclient.destroy();
                        },

                        section: Y.WidgetStdMod.FOOTER
                    },
                    {
                        value: "Cancel",

                        // The `action` property takes the Function that should be
                        // executed on a click event.
                        action: function(e) {
                            dia_edit_smbclient.hide();
                            dia_edit_smbclient.destroy();
                        },

                        section: Y.WidgetStdMod.FOOTER
                    }                    
                ]       
                
            }); 
        }       
    }
    
    /**
     * convert share array to field value
     */
    var shrarrtofield=function(){
        var txtval='';
        var tokens=['CS','CH','R']
        for (var s in shrarr){
            var shrrow=shrarr[s];
            txtval+=txtval=='' ? '' : "\n";
            var filters='';
            var farr=shrrow.filters.split(',');
            for (var f in farr){
                var fitem=farr[f];
                // prefix with : if its a token
                for (var tn in tokens){
                    var token=tokens[tn];
                    if (fitem.indexOf(token+'[')==0){
                        fitem=':'+fitem;
                    }
                }
                
                if (Y.Lang.trim(fitem)!=''){
                    filters+=','+Y.Lang.trim(fitem);
                }
            }
            txtval+=shrrow.name+' | '+shrrow.path+filters;
        }
        var shrta=Y.one("#id_s_smb_web_client_shares");
        shrta.setContent(txtval);
    }
    
    /**
     * convert field value to share array
     * @return Array shrarr
     */
    var fieldtoshrarr=function(){
       var shrta=Y.one("#id_s_smb_web_client_shares");
       var shrcontent=(shrta.getContent());
       var shrarr=[];
       if (shrcontent!=''){
           tmpa=shrcontent.split("\n");
           for (var v=0; v<tmpa.length; v++){
               var shrrow=tmpa[v];
               var tmpb=shrrow.split("|");
               var shrname=Y.Lang.trim(tmpb[0]);
              
               
               var tmpc=tmpb[1].split(":");
               var shrpath=Y.Lang.trim(tmpc[0]);
               if (shrpath.substring(shrpath.length-1,shrpath.length)==','){
                   shrpath=shrpath.substring(0,shrpath.length-1);
               };                
               
               var filters='';
               if (tmpc.length>1){
                   for (var f=1; f<tmpc.length; f++){
                       filters+=filters=='' ? '' : ','+"\n";
                       var fval=tmpc[f];
                       if (fval.substring(fval.length-1,fval.length)==','){
                           fval=fval.substring(0,fval.length-1);
                       };
                       filters+=fval;
                   }
               }
               var actions=shareactions(v);
           

               shrarr.push({actions:actions, id:v, name:shrname, path:shrpath, filters:filters});
           }
       }
       return shrarr;
    }

    var tmpa=Y.version.split('.');
    var verno=tmpa[0];
    var vpoint='';
    for (var v=1; v<tmpa.length; v++){
        
        // bug fix - cope with double digit decimal places
        var pnt=tmpa[v];
        if ((pnt+'').length<2){
            pnt='0'+pnt;
        }
        
        vpoint+=''+pnt;       
    }
    verno+='.'+vpoint;

    // get share details
    var shrarr=fieldtoshrarr();

    // if shrarr is empty then add 1 empty row
    /*
    if (shrarr.length==0){
        var actions=shareactions(0);  
        shrarr[{actions:actions, id:0, name:'', path:'', filters:''}];
    }
    */
    
    // create filter label
    var filterlab=Y.Node.create('<div>'+M.str.block_smb_web_client.jsintfilter+'</div>');
    var shrta=Y.one("#id_s_smb_web_client_shares");     
    shrta.insert(filterlab,'after');    
    
    // create container for datatable
    var tabcont=Y.Node.create('<div id="sharetablecont"><div id="controls"><button onclick="return false;" id="add">Add</button><button onclick="return false;" id="cancel" style="display:none;">Cancel</button></div><div id="sharetable"></div></div>');       
    shrta.insert(tabcont,'after');


    var ashrs=Y.one('#admin-shares');
    ashrs.one('.form-defaultinfo').setStyle('display','none');
    ashrs.one('.form-description').setStyle('display','none');
    shrta.setStyle('display','none');


    // create columnset
    var cols = [{key:"actions",label:"",allowHTML: true},{key:"name", label:"Share Name", emptyCellValue:'-', quickEdit: true},{key:"path", label:"Share", emptyCellValue:'-', quickEdit: true},{key:"filters", label:"Filter", emptyCellValue:'-', quickEdit: true}];

    var table=false;
    if (verno>=3.041 && verno<3.05){ // bug fix - cope with double digit decimal places
        // create datatable and render
        table = new Y.DataTable.Base({
            columnset: cols
        });            
    } else if (verno>=3.05){ // bug fix - cope with double digit decimal places
        table = new Y.DataTable.Base({
            columns: cols
        });             
    }
    if (!table){
        throw "data table was not instantiated correctly";
        shrta.setStyle('display','');        
        return;
    }



    var datasrc=new Y.DataSource.Function({
        source:function(request){return (shrarr)}
    });


    datasrc.plug(Y.Plugin.DataSourceJSONSchema, {
        schema: {
            resultFields: ["actions","name", "path", "filters"]
        }               
    });




    table.plug(Y.Plugin.DataTableDataSource, {
        datasource: datasrc
    });
    //table.plug(Y.Plugin.DataTableQuickEdit, {changesAlwaysInclude: ['id']});

    datasrc.after('data',function(e){                
        // update field value
        shrarrtofield();

        // add action events
        for (var s in shrarr){
            var shr=shrarr[s];

            (function(shr){
                Y.one("#delshare_"+shr.id).on('click', function(){
                   deleteshare(shr);
                });
                Y.one("#editshare_"+shr.id).on('click', function(){
                   editshare(shr);
                });
            })(shr);

        }
    });            

    table.render('#sharetable');
    table.datasource.load({request:null});

    var start  = Y.one('#start');
    var add  = Y.one('#add');
    var update   = Y.one('#update');
    var cancel = Y.one('#cancel');

    add.on('click', function(){

       var actions=shareactions(shrarr.length);      

       shrarr.push({actions:actions, id:shrarr.length, name:'',path:'',filters:''});
       table.render('#sharetable');
       table.datasource.load({request:null});
    });

        
        

    


}