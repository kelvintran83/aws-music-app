package com.example;

import software.amazon.awssdk.auth.credentials.ProfileCredentialsProvider;
import software.amazon.awssdk.regions.Region;
import software.amazon.awssdk.services.dynamodb.DynamoDbClient;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.util.Iterator;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

public class App {

        public static void main(String[] args) {
                ProfileCredentialsProvider credentialsProvider = ProfileCredentialsProvider.create();
                Region region = Region.US_EAST_1;
                DynamoDbClient ddb = DynamoDbClient.builder()
                                .credentialsProvider(credentialsProvider)
                                .region(region)
                                .build();
                String tableName = "Music";
                JSONParser jsonParser = new JSONParser();
                CreateTable.createTableComKey(ddb, tableName); // call method that create dynamoDB Table for music with
                                                               // a composite key
                System.out.println("Table " + tableName + " is being made");
                System.out.println("Wait 30 seconds for table to finish being made");
                try {
                        Thread.sleep(30000);
                } catch (InterruptedException e) {
                        Thread.currentThread().interrupt();
                }
                try { // call method that parses json file of music data and iterates through it,
                      // uploading it's string data into the previously made table

                        JSONObject jsonObject = (JSONObject) jsonParser
                                        .parse(new FileReader("D://CloudComputing//a1.json"));

                        JSONArray songs = (JSONArray) jsonObject.get("songs");
                        Iterator<Object> iterator = songs.iterator();
                        while (iterator.hasNext()) {
                                JSONObject song = (JSONObject) iterator.next();
                                String title = (String) song.get("title");
                                String artist = (String) song.get("artist");
                                String year = (String) song.get("year");
                                String web_url = (String) song.get("web_url");
                                String img_url = (String) song.get("img_url");

                                PutItems.putItemInTable(ddb, tableName, "artist", artist, "title", title,
                                                "year",
                                                year, "web_url", web_url, "img_url", img_url);
                                System.out.println("Added " + title + "!");

                        }
                        ddb.close();

                } catch (FileNotFoundException e) {
                        e.printStackTrace();
                } catch (IOException e) {
                        e.printStackTrace();
                } catch (ParseException e) {
                        e.printStackTrace();
                }

                try { // here this code downloads images from the provided links in the json file
                        JSONObject jsonObject = (JSONObject) jsonParser
                                        .parse(new FileReader("D://CloudComputing//a1.json"));

                        JSONArray songs = (JSONArray) jsonObject.get("songs");
                        Iterator<Object> iterator = songs.iterator();
                        while (iterator.hasNext()) {
                                JSONObject song = (JSONObject) iterator.next();
                                String title = (String) song.get("title");
                                String img_url = (String) song.get("img_url");

                                S3Images.downloadImage(title + ".jpg", img_url); // call method that will use referenced
                                                                                 // url to download images locally
                                System.out.println("Downloaded image for " + title);
                        }

                } catch (FileNotFoundException e) {
                        e.printStackTrace();
                } catch (IOException e) {
                        e.printStackTrace();
                } catch (ParseException e) {
                        e.printStackTrace();
                }

                try { // this code will upload the images stored locally into a s3 bucket that has
                      // already been made
                        JSONObject jsonObject = (JSONObject) jsonParser
                                        .parse(new FileReader("D://CloudComputing//a1.json"));

                        JSONArray songs = (JSONArray) jsonObject.get("songs");
                        Iterator<Object> iterator = songs.iterator();
                        while (iterator.hasNext()) {
                                JSONObject song = (JSONObject) iterator.next();
                                String title = (String) song.get("title");

                                S3Images.uploadImage("s3781137-cc-songimages", "D://CloudComputing//" + title + ".jpg");
                                System.out.println("Uploaded image for " + title);
                        }

                } catch (FileNotFoundException e) {
                        e.printStackTrace();
                } catch (IOException e) {
                        e.printStackTrace();
                } catch (ParseException e) {
                        e.printStackTrace();
                }
        }

}