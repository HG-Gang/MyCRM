@extends('user.layout.main_right')
<style>
    .edui-editor {
        margin-left: 32px;
    }
</style>
@section('content')
    <form class="layui-form" action="" id="AdminNewsEditForm" style="margin-top: 8px;">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">新闻标题</label>
                <div class="layui-input-block">
                    <input type="text" name="newstitle" id="newstitle" value="{{ $newsInfo['news_title'] }}" autocomplete="off" placeholder="请输入新闻标题" class="layui-input" style="width: 924px;">
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">是否推送</label>
            <div class="layui-input-block">
                @if($newsInfo['is_push'] == '0')
                    <input type="radio" name="is_push" value="0" title="不推送" checked>
                @else
                    <input type="radio" name="is_push" value="0" title="不推送">
                @endif
                @if($newsInfo['is_push'] == '1')
                    <input type="radio" name="is_push" value="1" title="推送" checked>
                @else
                    <input type="radio" name="is_push" value="1" title="推送">
                @endif
            </div>
        </div>
        <div class="layui-form-item">
            <textarea id="newscontect">{{ $newsInfo['news_content'] }}</textarea>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" onclick="saveNews()">保存</button>
            </div>
        </div>
    </form>
    <!-- 配置文件 -->
    <script type="text/javascript" src="{{ URL::asset('admin/lib/ueditor/1.4.3/ueditor.config.js') }}?ver={{ resource_version_number() }}"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="{{ URL::asset('admin/lib/ueditor/1.4.3/ueditor.all.min.js') }}?ver={{ resource_version_number() }}"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        //ue.execCommand("getlocaldata") 获取富文本Html格式内容
        var ue = UE.getEditor('newscontect', {
            initialFrameWidth:1000,  //初始化编辑器宽度,默认1000
            initialFrameHeight:500,  //初始化编辑器高度,默认320
            indentValue:"0.2em",
            initialStyle:"p{line-height:1em}",
            autoHeight: false,
        });

        function check_title_content() {
            if ($.trim($("#newstitle").val()) == "") {
                errorTips("新闻标题不能为空!", "msg", "newstitle");
            } else if (!ue.hasContents()) {
                errorTips("新闻内容不能为空!", "msg", "newscontect");
            } else {
                return true;
            }
        }
        
        function saveNews() {
            if (check_title_content()) {
                var formData = new FormData();
                formData.append("newsId", "{{ $newsInfo['news_id'] }}");
                formData.append("newsTitle", $.trim($("#newstitle").val()));
                formData.append("newsContent", ue.getContent());
                formData.append("ispush", $("input[name='is_push']:checked").val());
                formData.append("_token", "{{ csrf_token() }}");

                var index1 = openLoadShade();
                $.ajax({
                    url: route_prefix() + "/news/news_update",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dateType: "JSON",
                    type: "POST",
                    async: false,
                    success: function (data) {
                        if (data.msg == "FAIL") {
                            layer.msg("更新失败", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    closeLoadShade(index1);
                                }
                            });
                        } else if (data.msg == "SUC") {
                            layer.msg("更新成功", {
                                time: 20000, //20s后自动关闭
                                btn: ['知道了'],
                                yes: function (index, layero) {
                                    parent.layer.closeAll();
                                    window.location.href = "{{url(route_prefix() . '/news/news_list_browse')}}";
                                }
                            });
                        }
                    },
                    error: function () {
                        layer.msg("未知错误,请尝试重新操作或联系客服.");
                    }
                });
            }
        }
    </script>
@endsection

@section('custom-resources')
@endsection
