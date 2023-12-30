/**
 * GaoDe Map 高德地图
 */
jQuery(document).ready(function($) {
  $(function() {
    var map = new AMap.Map('gaode-map', {
      zoom: 13, // Set the initial zoom level
      center: [116.47332, 39.88475] // Set the center point (Longitude, Latitude)
    });


    var marker = new AMap.Marker({
      position: new AMap.LngLat(116.47332, 39.88475),   // 经纬度对象，也可以是经纬度构成的一维数组[119.920486,30.245285]
      title: '万博互联'
    });

    // ==================异步加载1个插件==================
    AMap.plugin('AMap.ToolBar',function() {
      var toolbar = new AMap.ToolBar();
      map.addControl(toolbar);
    });

    // 将创建的点标记添加到已有的地图实例：
    map.add(marker);
    marker.on('click',function(e){
      marker.markOnAMAP({
        name:'万博互联',
        position:marker.getPosition()
      })
    })
  });
});
