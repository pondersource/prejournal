<template>
    <!--
    SPDX-FileCopyrightText: Ponder Source Foundation <michiel@pondersource.com>
    SPDX-License-Identifier: AGPL-3.0-or-later
    -->
	<div id="content" class="app-prejournal">
		<AppNavigation>
			<ul>
				<AppNavigationItem v-for="note in notes"
					:key="note.id"
					:title="note.title"
					:class="{active: currentNoteId === note.id}"
					@click="openNote(note)">
				</AppNavigationItem>
			</ul>
		</AppNavigation>
		<AppContent>
			<div v-if="currentNote">
				<br><br>
				Format:
				<select ref="contentType" v-model="currentNote.contentType" :disabled="updating">
					<option value="">--Please choose an option--</option>
					<option value="saveMyTime-CSV">Save My Time</option>
					<option value="scoro-JSON">Scoro</option>
					<option value="stratustime-JSON">Stratus</option>
					<option value="time-CSV">Wbbly Time</option>
					<option value="timeBro-CSV">TimeBro</option>
					<option value="timecamp-CSV">Timecamp</option>
					<option value="timeDoctor-CSV">Time Doctor</option>
					<option value="timely-CSV">Timely</option>
					<option value="timeManager-CSV">Time Manager</option>
					<option value="timesheet-CSV">Timesheet Urenapp</option>
					<option value="timesheetMobile-CSV">Timesheet Mobile</option>
					<option value="timetip-JSON">Timetip</option>
					<option value="timetracker-XML">Anuko Timetracker</option>
					<option value="timeTrackerCli-JSON">Time Tracker CLI</option>
					<option value="timeTrackerDaily-CSV">eBillity Time tracker</option>
					<option value="timeTrackerNextcloud-JSON">Nextcloud Time Tracker</option>
					<option value="veryfiTime-JSON">Veryfi Timesheets</option>
				</select>
				<textarea ref="content" v-model="currentNote.content" :disabled="updating" />
				<input type="button"
					class="primary"
					:value="t('prejournal', 'Import')"
					:disabled="updating || !savePossible"
					@click="importFile">
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{
				 t('prejournal', 'Select a timetracker export file to get started') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationNew,
	},
	data() {
		return {
			notes: [],
			currentNoteId: null,
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected note object
		 * @returns {Object|null}
		 */
		currentNote() {
			if (this.currentNoteId === null) {
				return null
			}
			return this.notes.find((note) => note.id === this.currentNoteId)
		},

		/**
		 * Returns true if a note is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.title !== ''
		},
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/prejournal/notes'))
			this.notes = response.data
		} catch (e) {
			console.error(e)
			showError(t('prejournal', 'Could not fetch notes'))
		}
		this.loading = false
	},

	methods: {
		/**
		 * Create a new note and focus the note content field automatically
		 * @param {Object} note Note object
		 */
		openNote(note) {
			if (this.updating) {
				return
			}
			this.currentNoteId = note.id
			console.log(`current note id ${this.currentNoteId}`);
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		async importFile() {
			this.updating = true
			console.log(this.currentNote);
			console.log(this.currentNoteId);
			
			try {
				await axios.post(generateUrl(`/apps/prejournal/import`), {
					contentType: this.currentNote.contentType,
					file: this.currentNote.title,
					content: this.currentNote.content
			  });
			} catch (e) {
				console.error(e)
				showError(t('prejournal', 'Could not import the file'))
			}
			this.updating = false
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * The note is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNote() {
			if (this.currentNoteId !== -1) {
				this.currentNoteId = -1
				this.notes.push({
					id: -1,
					title: '',
					content: '',
				})
				this.$nextTick(() => {
					this.$refs.title.focus()
				})
			}
		},
		/**
		 * Abort creating a new note
		 */
		cancelNewNote() {
			this.notes.splice(this.notes.findIndex((note) => note.id === -1), 1)
			this.currentNoteId = null
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createNote(note) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/prejournal/notes'), note)
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId)
				this.$set(this.notes, index, response.data)
				this.currentNoteId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('prejournal', 'Could not create the note'))
			}
			this.updating = false
		},
		/**
		 * Update an existing note on the server
		 * @param {Object} note Note object
		 */
		async updateNote(note) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/prejournal/notes/${note.id}`), note)
			} catch (e) {
				console.error(e)
				showError(t('prejournal', 'Could not update the note'))
			}
			this.updating = false
		},
		/**
		 * Delete a note, remove it from the frontend and show a hint
		 * @param {Object} note Note object
		 */
		async deleteNote(note) {
			try {
				await axios.delete(generateUrl(`/apps/prejournal/notes/${note.id}`))
				this.notes.splice(this.notes.indexOf(note), 1)
				if (this.currentNoteId === note.id) {
					this.currentNoteId = null
				}
				showSuccess(t('prejournal', 'Note deleted'))
			} catch (e) {
				console.error(e)
				showError(t('prejournal', 'Could not delete the note'))
			}
		},
	},
}
</script>
<style scoped>
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	input[type='text'] {
		width: 100%;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}
</style>
