<template>
	<div>
        <button class="btn m-0" @click="toggleFullscreen()">
            <template v-if="!fullscreen">
                <i class="fas fa-expand text-dark"></i>
            </template>
            <template v-else>
                <i class="fas fa-compress"></i>
            </template>
        </button>
	</div>
</template>

<script type="text/javascript">

export default {

    data() {
        return {
            fullscreen : false,
        }
    },

    methods : {
        toggleFullscreen(elem) {
            this.fullscreen = this.fullscreen ? false : true;
            elem = elem || document.documentElement;
            if (!document.fullscreenElement && !document.mozFullScreenElement &&
                !document.webkitFullscreenElement && !document.msFullscreenElement) {
                if (elem.requestFullscreen) {
                elem.requestFullscreen();
                } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                if (document.exitFullscreen) {
                document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
                }
            }
        }
    }
}
</script>
