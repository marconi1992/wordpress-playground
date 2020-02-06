const express = require('express');

const app = express();

const initialData = require('./mock-data.json');

app.get('/news', (req, res) => {
	return res.json(initialData);
});

app.listen(3000, () => {
	console.log('Server listen on http://localhost:3000');
});
