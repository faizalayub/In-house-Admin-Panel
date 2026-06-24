import Button from './Button.vue'
import Drawer from './Drawer.vue'
import InputText from './InputText.vue'
import IconField from './IconField.vue'
import InputIcon from './InputIcon.vue'
import Dialog from './Dialog.vue'
import Toast from './Toast.vue'
import DataTable from './DataTable.vue'
import Paginator from './Paginator.vue'
import Card from './Card.vue'
import Panel from './Panel.vue'
import Checkbox from './Checkbox.vue'
import Tag from './Tag.vue'
import Chip from './Chip.vue'

export default {
    install(app) {
        app.component('Button', Button)
        app.component('InputText', InputText)
        app.component('IconField', IconField)
        app.component('InputIcon', InputIcon)
        app.component('Dialog', Dialog)
        app.component('Toast', Toast)
        app.component('DataTable', DataTable)
        app.component('Paginator', Paginator)
        app.component('Card', Card)
        app.component('Panel', Panel)
        app.component('Checkbox', Checkbox)
        app.component('Tag', Tag)
        app.component('Chip', Chip)
        app.component('Drawer', Drawer)
    }
}
