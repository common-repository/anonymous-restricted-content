(function (wp) {
  var el = wp.element.createElement;
  var CheckboxControl = wp.components.CheckboxControl;
  var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
  var registerPlugin = wp.plugins.registerPlugin;
  var withSelect = wp.data.withSelect;


  function checkboxChange(e){
    wp.data.dispatch( 'core/editor' ).editPost(
      { meta: { arc_restricted_post: e } }
    );
  }


  var mapSelectToProps = function( select ) {
    return {
      metaFieldValue: select( 'core/editor' )
      .getEditedPostAttribute( 'meta' )
      [ 'arc_restricted_post' ]
    }
  }


  function ArcPostStatusInfo(props) {
    return el(
      PluginPostStatusInfo,
      {
        className: 'my-plugin-post-status-info',
      },
      el(
        CheckboxControl, {
          label: ArcLStrings.RestrictedForAnonymousUsers,
          checked: props.metaFieldValue,
          id: 'arc_checkbox',
          onChange: checkboxChange
        }
      )
    )
  }

  var MetaBlockFieldWithData = withSelect( mapSelectToProps )( ArcPostStatusInfo );

  registerPlugin( 'arc-plugin', {
    render: MetaBlockFieldWithData
  } );


})(
  window.wp
)
