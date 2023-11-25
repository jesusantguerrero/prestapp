import { IUnit } from './../Modules/properties/propertyEntity';
import { supabase } from "@/plugins/supabase"

export const propertyService = {
  async createBucket() {
    const { data, error } = await supabase.storage.createBucket('properties', {
      public: true,
      allowedMimeTypes: ['image/*'],
      fileSizeLimit: "1MB"
    })
  },
  async saveImage(unit: IUnit, file: File, fileName?: string) {
    console.log(unit)
    const { data, error } = await supabase.storage.from('properties')
    .upload(`${fileName}`, file);

    if (error) {
      throw new Error('bad file')
    }
    return data;
  },

  async getImages(unit: IUnit, file: File, fileName?: string) {
    console.log(unit)
    const { data, error } = await supabase.storage.from('properties')
    .upload(`${fileName}`, file);

    if (error) {
      throw new Error('bad file')
    }
    return data;
  }
}
