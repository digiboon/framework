const framework = {

	/**
	 * Sends data to an AJAX action.
	 *
	 * Note that this expects you have configured the back-end
	 * to allow for receiving this action. It can be done easily with
	 * `Baboon::ajaxAction` PHP method.`
	 *
	 * @param {string} action The name of the action to target
	 * @param {object} data The data sent to the back-end
	 */
	ajaxAction: (action, data) => {

		return new Promise((resolve, reject) => {

			// Set data
			const formData = new FormData()

			Object.keys(data).forEach(key => {

				formData.append(key, data[key])

			})

			// Set action
			formData.append('action', action)

			// Send data
			fetch(_frameworkAjaxURL, {
				method: 'POST',
				body: formData
			}).then(response => {

				resolve(response)

			}).catch(error => {

				reject(error)

			})

		})

	}
}