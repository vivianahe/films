<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{title}}</div>
                    <div class="card-body">
                        <template v-if="tbl_films">
                            <b-row class="justify-content-end">
                                <div class="col-2">
                                    <b-button variant="success" class="text-white" @click="clear(), tbl_films=false, title='AGREGAR PELÍCULA'">Agregar <i class="bi bi-plus-lg"></i></b-button>
                                </div>
                            </b-row>
                            <vue-bootstrap4-table :rows="rows" :columns="columns" :config="config" :classes="classes">
                                <template slot="state" slot-scope="props" >
                                    <div>
                                        <b-form-checkbox v-if="props.row.state === 'active'" switch @change="stateFilm(props.row.options, 'inactive','Inactivo')" v-model="checkboxes[props.row.vbt_id-1]">Activo</b-form-checkbox>
                                        <b-form-checkbox v-else-if="props.row.state === 'inactive'" switch @change="stateFilm(props.row.options, 'active', 'Activo')" v-model="checkboxes[props.row.vbt_id-1]">Inactivo</b-form-checkbox>
                                    </div>
                                </template>
                                <template slot="img_film" slot-scope="props">
                                    <b-button variant="outline-danger" size="sm" @click="openDocument(props.row.img_film)">Ver </b-button>
                                </template>
                                <template slot="options" slot-scope="props">
                                    <b-button variant="warning" size="sm" @click="getFilmId(film_id = props.cell_value, title='EDITAR PELÍCULA')"> <i class="bi bi-pencil-square"></i></b-button>
                                </template>
                                <template slot="empty-results" class="text-center">
                                    <div class="text-center">
                                        <p>No se encontraron elementos para mostrar</p>
                                    </div>
                                </template>
                            </vue-bootstrap4-table>
                        </template>
                        <template v-else>
                            <b-row class="justify-content-end">
                                <div class="col-2">
                                    <b-button variant="secondary" class="text-white" @click="tbl_films=true, title='PELICULAS'"><i class="bi bi-arrow-left"></i> Regresar </b-button>
                                </div>
                            </b-row>
                            <b-form>
                                <b-row class="justify-content-center">
                                    <b-col cols="6">
                                        <b-form-group label="Nombre:*">
                                            <b-form-textarea
                                                id="textarea-default"
                                                v-model="name"
                                                @blur="verifyFilm('name')"
                                            ></b-form-textarea>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row class="justify-content-center">
                                    <b-col cols="6">
                                        <b-form-group label="Productor:*">
                                            <b-form-input
                                                v-model="productor"
                                                type="text"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row class="justify-content-center">
                                    <b-col cols="3">
                                        <b-form-group label="Duración - Horas:*">
                                            <b-form-input
                                                v-model="hour"
                                                type="number"
                                                @keypress="onlyNumber"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col cols="3">
                                        <b-form-group label="Minutos:*">
                                            <b-form-input
                                                v-model="minute"
                                                type="number"
                                                @keypress="onlyNumber"
                                                required
                                            ></b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row><b-row class="justify-content-center">
                                    <b-col cols="6">
                                        <b-form-group label="Género:*">
                                            <b-form-select v-model="gender" :options="options_gender" required></b-form-select>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row class="justify-content-center">
                                    <b-col cols="6">
                                        <b-form-group label="Imagen:*">
                                            <div class="col text-center mt-3" >
                                                <div v-if="show_image">
                                                    <div class="col-md-12">
                                                        <img id="size_image" v-bind:src="'/soportes/film/'+this.img_film" width="200" height="150">
                                                    </div>
                                                    <div class="col-md-12 mt-3 text-center">
                                                        <button class="btn btn-success text-white" @click="changeImage">Modificar Imagen <i class="bi bi-arrow-bar-up"></i></button>
                                                    </div>
                                                </div>
                                                <vue-dropzone v-else ref="myVueDropzone" id="dropzone1" :options="dropzoneOptions" @vdropzone-success="uploadDropzoneFinish" required></vue-dropzone>
                                            </div>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col class="text-center mt-3" v-if="loadCreate">
                                        <b-button variant="success" class="text-white" @click="setFilm">Guardar</b-button>
                                    </b-col>
                                    <b-col class="text-center" v-else>
                                        <i class="bi bi-arrow-repeat"></i> Guardando...
                                    </b-col>
                                </b-row>
                            </b-form>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import vue2Dropzone from "vue2-dropzone";
import VueBootstrap4Table from "vue-bootstrap4-table";
    export default {
        mounted() {
            this.getGenderActive()
            this.getFilm()
        },
        data() {
            return {
                loadCreate: true,
                dropzoneOptions: {
                    url: 'https://httpbin.org/post',
                    thumbnailWidth: 200,
                    //maxFilesize: 1,
                    maxFiles: 1,
                    acceptedFiles: 'image/*',
                    addRemoveLinks: true,
                    dictDefaultMessage: "<i class='bi bi-cloud-arrow-up-fill'></i> Subir imagen",
                    headers: { "My-Awesome-Header": "header value" },
                },
                title: 'PELICULAS',
                film_id: '',
                name: '',
                productor: '',
                hour: 0,
                minute: 0,
                gender: null,
                options_gender: [],
                img_film: [],
                show_image: false,
                tbl_films: true,
                checkboxes: [],
                rows: [],
                columns: [
                    {label: "#", name: "No",},
                    {label: "Nombre",name: "name", sort: true, filter: {type: "simple", placeholder: ""},},
                    {label: "Productor",name: "productor", sort: true, filter: {type: "simple", placeholder: ""},},
                    {label: "Duración",name: "duration", sort: true, filter: {type: "simple", placeholder: ""},},
                    {label: "Género",name: "gender", sort: true, filter: {type: "simple", placeholder: ""},},
                    {label: "Imagen",name: "img_film", sort: true},
                    {label: "Estado",name: "state", sort: true},
                    {label: "Opciones",name: "options"},
                ],
                config: {
                    show_refresh_button: false,
                    show_reset_button: false,
                    card_mode: false,
                    per_page: 10,
                    global_search: {
                        placeholder: 'Buscar',
                        showClearButton: false
                    },
                },
                classes: {
                    table: {
                        "table table-striped table-striped-bg-default mt-3": true,
                    },
                },
            }
        },
        methods: {
            changeImage(){
                this.show_image = false;
                this.img_film = [];
            },
            uploadDropzoneFinish(file) {
                this.img_film = [];
                this.img_film.push(file);
            },
            openDocument: function(doc){
                window.open('/soportes/film/'+doc);
            },
            getFilm: function () {
                axios.get('/resource-film?Q=1').then(response => {
                    this.rows = [];
                    let role_aux = [];
                    let checkboxes_aux = [];
                    let count = 1;
                    for(let i in response.data){
                        role_aux.push({"No": count++, "name": response.data[i].name, "productor": response.data[i].productor,"duration": response.data[i].hour+' h '+response.data[i].minute+' m', "gender": response.data[i].gender,"img_film": response.data[i].img_film,"state": response.data[i].state, "user": response.data[i].user,"options": response.data[i].id});
                        checkboxes_aux.push(response.data[i].state == 'active' ? true : false);
                    }
                    this.rows = role_aux;
                    this.checkboxes = checkboxes_aux;
                }).catch(e => {
                    console.log(e.response);
                });
            },
            setFilm: function() {
                if (this.img_film.length != 0) {
                    this.loadCreate = false;
                    let formData = new FormData;
                    formData.append('name', this.name);
                    formData.append('productor', this.productor);
                    formData.append('img_film', this.img_film[0]);
                    formData.append('hour', this.hour);
                    formData.append('minute', this.minute);
                    formData.append('gender', this.gender);
                    formData.append('id', this.film_id);
                    const config = {
                        headers: {'content-type': 'multipart/form-data'}
                    };
                    axios.post('/resource-film', formData, config).then(response => {
                        this.tbl_films = true;
                        this.getFilm();
                        this.messageAlert('success', 'Correcto!', 'Película guardada con éxito!');
                        this.loadCreate = true;
                        this.clear();
                    }).catch(e => {
                        console.log(e);
                        this.loadCreate = true;
                    });
                } else {
                    this.messageAlert('warning', 'Atención!', 'Debes ingresar todos los campos.');
                }
            },
            getFilmId: function() {
                axios.get('/resource-film?Q=2&film_id=' + this.film_id).then((response) => {
                    this.img_film=[];
                    this.tbl_films = false;
                    this.name = response.data.name;
                    this.productor = response.data.productor;
                    this.hour = response.data.hour;
                    this.minute = response.data.minute;
                    this.gender = response.data.gender_id;
                    this.img_film.push(response.data.img_film);
                    if(this.img_film[0] != null){
                        this.show_image = true;
                    } else {
                        this.show_image = false;
                    }
                }).catch((error) => {
                    console.log(error);
                });
            },
            getGenderActive: function () {
                axios.get('/resource-film?Q=0').then(response => {
                    this.options_gender = [];
                    this.options_gender.push({
                        value: null,
                        text:'Selecciona una opción'
                    })
                    for(let i = 0; i < response.data.length; i++){
                        this.options_gender.push({
                            'value': response.data[i].id,
                            'text': response.data[i].name
                        })
                    }
                }).catch(e=>{
                    console.log(e.response);
                });
            },
            stateFilm: function(cellar_id, state) {
                let formData = {
                    id: cellar_id,
                    state: state
                };
                axios.put('/resource-film/'+cellar_id,formData).then((response) => {
                    this.getFilm();
                    this.messageAlert('success','OK!','Estado actualizado.');
                }).catch((error) => {
                    console.log(error.response);
                });

            },
            //Verificar nombre película
            verifyFilm: function (data) {
                let param = this.name;
                axios.get('/resource-film?Q=3&name=' + param).then((response) => {
                    let result = response.data;
                    if (result) {
                        this.name = '';
                        this.messageAlert('warning', 'Atención', 'El nombre de la película ingresado ya existe.');
                    }

                }).catch((error) => {
                    console.log(error.response);
                });
            },
            //Digitar solo números
            onlyNumber ($event) {
                let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                if (keyCode < 48 || keyCode > 57 || keyCode == 46) {
                    $event.preventDefault();
                }
            },
            //Limpiar frm
            clear: function() {
                this.name = '';
                this.productor = '';
                this.img_film = [];
                this.hour = 0;
                this.minute = 0;
                this.film_id = '';
                this.gender = null;
                this.show_image = false;
            },
            messageAlert: function(state, title, msj) {
                this.$swal({
                    icon: state,
                    title: title,
                    text: msj
                });
            },
        },
        components: {
            vueDropzone: vue2Dropzone,
            VueBootstrap4Table
        }
    }
</script>
