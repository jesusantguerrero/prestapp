import { supabase } from "@/plugins/supabase"

export const property = {
  async saveImage(userId: number, file: File, fileName?: string) {
    const { data, error } = await supabase.storage.from('properties')
    .upload(`${userId}/${fileName || file.name}`, file);

    if (error) throw new Error('bad file')
    return data;

  }
}
