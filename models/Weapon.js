const mongoose = require('mongoose');

const weaponSchema = new mongoose.Schema({
  name: {
    type: String,
    required: true
  },
  type: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  characteristics: {
    material: String,
    weight: Number,
    length: Number,
    image : String
  }
});

module.exports = mongoose.model('Weapon', weaponSchema);
