<template>

</template>
<script>
    export default {
        component : 'Bus',
        data() {
            return {
                caption : 'test',
                pin_id : null,
                scheduled_at : null,
                pages : {},
                token : null,
                active_page_name : null,
                activated_page : null,
                messages : [],
                changed : false,
                boards : {},
                active_board:'',
                connectUrl : null,
                img_obj : {},
                pin : {},
                instagram : {},
            }
        }
    }
</script>