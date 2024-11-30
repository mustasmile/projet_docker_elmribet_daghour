const mongoose = require('mongoose');

const weaponSchema = new mongoose.Schema({
  name: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  characteristics: {
    material: {
      type: String,
    },
    weight: {
      type: Number,
    },
    length: {
      type: Number,
    },
    image: {
      type: String,
    },
  },
});

const Weapon = mongoose.model('Weapon', weaponSchema);

module.exports = Weapon;
