const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const path = require('path');
const Weapon = require('./models/Weapon');

const app = express();
const port = 3000;


app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

const mongoUri = process.env.MONGO_URI || 'mongodb://root:example@mongo:27017/medieval_weapons?authSource=admin';
mongoose
  .connect(mongoUri, {
    useNewUrlParser: true,
    useUnifiedTopology: true,
  })
  .then(() => console.log('Connecté à MongoDB'))
  .catch((err) => console.error('Erreur de connexion à MongoDB:', err));


app.get('/', (req, res) => {
  res.send('Welcome to the Medieval Weapons API');
});


app.get('/api/weapons', async (req, res) => {
  try {
    const weapons = await Weapon.find();
    res.status(200).json(weapons);
  } catch (err) {
    console.error('Erreur de récupération des armes:', err);
    res.status(500).json({ message: 'Erreur de récupération des armes' });
  }
});


app.post('/api/weapons', async (req, res) => {
  const { name, type, description, characteristics } = req.body;

  const weapon = new Weapon({
    name,
    type,
    description,
    characteristics,
  });

  try {
    await weapon.save();
    res.status(201).json(weapon);
  } catch (err) {
    console.error('Erreur lors de l\'ajout de l\'arme:', err);
    res.status(400).json({ message: 'Erreur lors de l\'ajout de l\'arme', error: err.message });
  }
});


app.put('/api/weapons/:id', async (req, res) => {
  const { id } = req.params;
  const { name, type, description, characteristics } = req.body;

  try {
    const updatedWeapon = await Weapon.findByIdAndUpdate(
      id,
      { name, type, description, characteristics },
      { new: true }
    );

    if (!updatedWeapon) {
      return res.status(404).json({ message: `Weapon with ID ${id} not found` });
    }

    res.status(200).json(updatedWeapon);
  } catch (err) {
    console.error('Erreur lors de la mise à jour de l\'arme:', err);
    res.status(400).json({ message: 'Erreur lors de la mise à jour de l\'arme', error: err.message });
  }
});


app.delete('/api/weapons/:id', async (req, res) => {
  const { id } = req.params;

  try {
    const deletedWeapon = await Weapon.findByIdAndDelete(id);

    if (!deletedWeapon) {
      return res.status(404).json({ message: `Weapon with ID ${id} not found` });
    }

    res.status(200).json({ message: 'Weapon deleted successfully', weapon: deletedWeapon });
  } catch (err) {
    console.error('Erreur lors de la suppression de l\'arme:', err);
    res.status(500).json({ message: 'Erreur lors de la suppression de l\'arme', error: err.message });
  }
});


app.get('/api/weapons/:id', async (req, res) => {
  const { id } = req.params;

  try {
    const weapon = await Weapon.findById(id);

    if (!weapon) {
      return res.status(404).json({ message: `Weapon with ID ${id} not found` });
    }

    res.status(200).json(weapon);
  } catch (err) {
    console.error('Erreur lors de la récupération de l\'arme:', err);
    res.status(500).json({ message: 'Erreur lors de la récupération de l\'arme', error: err.message });
  }
});


app.use((req, res) => {
  res.status(404).json({ message: 'Route non trouvée' });
});


app.listen(port, () => {
  console.log(`Server started at http://localhost:${port}`);
});
