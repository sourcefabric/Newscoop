UPDATE topic_translations as tt INNER JOIN main_topics AS t ON tt.object_id = t.id SET tt.isDefault = 1 WHERE t.title = tt.content;
